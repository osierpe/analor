"""Parses and validates incoming search requests, then delegates to the
query builder and repository."""
from __future__ import annotations

import json
import logging

from ..query.where_clause_builder import InvalidSearchParameter, build_where_clause
from ..repositories.compound_repository import search_compounds

logger = logging.getLogger(__name__)


class InvalidSearchRequest(ValueError):
    """Raised when the incoming request cannot be turned into a query."""


def run_search(raw_data: str | None) -> list[dict]:
    if not raw_data:
        raise InvalidSearchRequest("Parâmetro 'data' é obrigatório")

    try:
        parameters = json.loads(raw_data)
    except json.JSONDecodeError as exc:
        raise InvalidSearchRequest("Parâmetro 'data' não é um JSON válido") from exc

    if not isinstance(parameters, dict):
        raise InvalidSearchRequest("Parâmetro 'data' deve ser um objeto JSON")

    try:
        where_clause, params = build_where_clause(parameters)
    except InvalidSearchParameter as exc:
        raise InvalidSearchRequest(str(exc)) from exc

    logger.info("executing search where clause: %s", where_clause)
    return search_compounds(where_clause, params)
