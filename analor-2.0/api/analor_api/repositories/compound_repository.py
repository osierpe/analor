"""Data access for compounds and abbreviations. This is the only module
that talks to the database directly."""
from __future__ import annotations

import sqlalchemy
from sqlalchemy.engine import CursorResult

from ..db import get_engine


def fetch_abbreviations() -> list[dict]:
    with get_engine().connect() as conn:
        result = conn.execute(
            sqlalchemy.text("SELECT nome, nlin, pchave FROM abrev ORDER BY nome")
        )
        return _rows_to_dicts(result)


def search_compounds(where_clause: str, params: dict) -> list[dict]:
    query = "SELECT * FROM analor_0_0_1"
    if where_clause:
        query += f" WHERE {where_clause}"
    query += " ORDER BY ncom"

    with get_engine().connect() as conn:
        result = conn.execute(sqlalchemy.text(query), params)
        return _rows_to_dicts(result)


def _rows_to_dicts(result: CursorResult) -> list[dict]:
    columns = list(result.keys())
    rows = []
    for row in result:
        row_dict = dict(zip(columns, row))
        cleaned = {
            key: str(value).strip() if value is not None else ""
            for key, value in row_dict.items()
        }
        rows.append(cleaned)
    return rows
