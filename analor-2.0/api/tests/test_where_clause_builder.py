import pytest

from analor_api.query.where_clause_builder import (
    InvalidSearchParameter,
    build_where_clause,
)


def test_empty_parameters_produce_no_clause():
    clause, params = build_where_clause({})
    assert clause == ""
    assert params == {}


def test_cas_and_nome_are_bound_not_interpolated():
    clause, params = build_where_clause({"cas": "50-00-0", "nome": "formal"})
    assert "50-00-0" not in clause
    assert "formal" not in clause
    assert clause == "cas = :cas AND ncom LIKE UPPER(:nome)"
    assert params == {"cas": "50-00-0", "nome": "%formal%"}


def test_formula_search_is_case_insensitive_exact_match_and_bound():
    clause, params = build_where_clause({"formula": "c6h12o6"})
    assert clause == "UPPER(fmol) = UPPER(:formula)"
    assert params == {"formula": "c6h12o6"}


def test_element_injection_attempt_is_rejected():
    malicious = {"elementos": [{"nome": "Carbono; DROP TABLE analor_0_0_1;--", "tem": True}]}
    with pytest.raises(InvalidSearchParameter):
        build_where_clause(malicious)


def test_known_element_resolves_to_whitelisted_column():
    clause, params = build_where_clause(
        {"elementos": [{"nome": "Carbono", "tem": True}, {"nome": "Oxigênio", "quantidade": 2}]}
    )
    assert clause == "carb > 0 AND oxig = :element_1"
    assert params == {"element_1": 2}


def test_unknown_property_is_rejected():
    with pytest.raises(InvalidSearchParameter):
        build_where_clause({"propriedades": [{"nome": "não existe", "alcance": [1, None]}]})


def test_non_numeric_quantity_is_rejected():
    with pytest.raises(InvalidSearchParameter):
        build_where_clause({"elementos": [{"nome": "Carbono", "quantidade": "1); DROP TABLE x;--"}]})


def test_carbon_skeleton_incsim_always_uses_wildcard():
    clause, params = build_where_clause(
        {"ecgf": [{"gFunc": "OH", "inex": "incSim"}]}
    )
    assert clause == "(LOWER(nlin) LIKE :skeleton_0)"
    assert params == {"skeleton_0": "%oh%"}


def test_carbon_skeleton_unknown_mode_is_rejected():
    with pytest.raises(InvalidSearchParameter):
        build_where_clause({"ecgf": [{"gFunc": "OH", "inex": "algo-invalido"}]})
