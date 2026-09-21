"""Builds the parameterized SQL WHERE clause for compound searches.

Every dynamic value is bound through a named SQLAlchemy parameter
(`:name`) instead of being interpolated into the SQL string. Column
names, which SQL parameter binding cannot cover, are never taken from
user input directly: they are only ever looked up from the fixed
whitelists below, so a request can never reference an arbitrary column
or break out of the query.
"""
from __future__ import annotations

from unidecode import unidecode

# Maps the element names the frontend sends (see src/form.ts) to their
# corresponding database columns.
_ELEMENT_COLUMNS = {
    "carbono": "carb",
    "hidrogenio": "hidr",
    "nitrogenio": "nitr",
    "oxigenio": "oxig",
    "fluor": "fluo",
    "cloro": "clor",
    "bromo": "brom",
    "iodo": "iodo",
    "enxofre": "enxo",
}

_PROPERTY_COLUMNS = {
    "peso molecular": "pmol",
    "ponto de fusão": "pf",
    "ponto de ebulição": "pe",
}

_SKELETON_MODES = {"incSim", "excluir", "incluir"}


class InvalidSearchParameter(ValueError):
    """Raised when a search parameter fails validation."""


def build_where_clause(parameters: dict) -> tuple[str, dict]:
    """Returns (sql_where_clause, bind_params). The clause is safe to
    interpolate into a query string as-is; every value it references
    goes through `bind_params`."""
    clauses: list[str] = []
    params: dict = {}

    if parameters.get("cas"):
        clauses.append("cas = :cas")
        params["cas"] = parameters["cas"]

    if parameters.get("nome"):
        clauses.append("ncom LIKE UPPER(:nome)")
        params["nome"] = f"%{parameters['nome']}%"

    if parameters.get("formula"):
        clauses.append("UPPER(fmol) LIKE UPPER(:formula)")
        params["formula"] = f"%{parameters['formula']}%"

    elements_clause, elements_params = _build_elements_clause(
        parameters.get("elementos") or []
    )
    if elements_clause:
        clauses.append(elements_clause)
        params.update(elements_params)

    props_clause, props_params = _build_properties_clause(
        parameters.get("propriedades") or []
    )
    if props_clause:
        clauses.append(props_clause)
        params.update(props_params)

    skeleton_clause, skeleton_params = _build_carbon_skeleton_clause(
        parameters.get("ecgf") or []
    )
    if skeleton_clause:
        clauses.append(skeleton_clause)
        params.update(skeleton_params)

    return " AND ".join(clauses), params


def _resolve_element_column(name: str) -> str:
    key = unidecode(str(name)).strip().lower()
    column = _ELEMENT_COLUMNS.get(key)
    if column is None:
        raise InvalidSearchParameter(f"Elemento desconhecido: {name!r}")
    return column


def _resolve_property_column(name: str) -> str:
    column = _PROPERTY_COLUMNS.get(str(name).strip().lower())
    if column is None:
        raise InvalidSearchParameter(f"Propriedade desconhecida: {name!r}")
    return column


def _as_int(value, field: str) -> int:
    try:
        return int(value)
    except (TypeError, ValueError) as exc:
        raise InvalidSearchParameter(f"Valor numérico inválido para {field}: {value!r}") from exc


def _build_elements_clause(elements: list[dict]) -> tuple[str, dict]:
    clauses = []
    params = {}
    for index, element in enumerate(elements):
        if "nome" not in element:
            raise InvalidSearchParameter("Elemento sem nome informado")
        column = _resolve_element_column(element["nome"])
        quantidade = element.get("quantidade", "")
        if quantidade not in ("", None):
            param_name = f"element_{index}"
            clauses.append(f"{column} = :{param_name}")
            params[param_name] = _as_int(quantidade, element["nome"])
        elif element.get("tem") is True:
            clauses.append(f"{column} > 0")
        elif element.get("tem") is False:
            clauses.append(f"{column} = 0")
    return " AND ".join(clauses), params


def _build_properties_clause(properties: list[dict]) -> tuple[str, dict]:
    clauses = []
    params = {}
    for index, prop in enumerate(properties):
        if "nome" not in prop:
            raise InvalidSearchParameter("Propriedade sem nome informado")
        column = _resolve_property_column(prop["nome"])
        low, high = (prop.get("alcance") or [None, None])[:2]
        if low not in (None, ""):
            param_name = f"prop_{index}_low"
            clauses.append(f"{column} >= :{param_name}")
            params[param_name] = _as_int(low, prop["nome"])
        if high not in (None, ""):
            param_name = f"prop_{index}_high"
            clauses.append(f"{column} <= :{param_name}")
            params[param_name] = _as_int(high, prop["nome"])
    return " AND ".join(clauses), params


def _build_carbon_skeleton_clause(entries: list[dict]) -> tuple[str, dict]:
    """Mirrors the original accumulation logic (each entry's connector
    depends on its own mode, so AND/OR can mix across entries in
    input order) but binds every pattern as a parameter and always
    wraps it in `%...%`, fixing a bug where the first `incSim` entry
    used to be compared for an exact match instead of a substring."""
    clause = ""
    params: dict = {}
    for index, entry in enumerate(entries):
        pattern = entry.get("gFunc")
        if not pattern:
            continue
        mode = entry.get("inex")
        if mode not in _SKELETON_MODES:
            raise InvalidSearchParameter(f"Modo de filtro desconhecido: {mode!r}")

        param_name = f"skeleton_{index}"
        params[param_name] = f"%{str(pattern).lower()}%"

        if mode == "incSim":
            condition = f"LOWER(nlin) LIKE :{param_name}"
            clause = f"{clause} AND {condition}" if clause else f"({condition}"
        elif mode == "excluir":
            condition = f"LOWER(nlin) NOT LIKE :{param_name}"
            clause = f"{clause} AND {condition}" if clause else f"({condition}"
        else:  # incluir
            condition = f"LOWER(nlin) LIKE :{param_name}"
            clause = f"{clause} OR {condition}" if clause else f"({condition}"

    if clause:
        clause += ")"
    return clause, params
