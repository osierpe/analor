"""Database connectivity.

Uses the Cloud SQL Python Connector to reach Cloud SQL from Cloud Run
without a public IP or manually managed SSL certs, wired into a
SQLAlchemy engine so connections are pooled instead of being opened and
torn down on every request.
"""
from __future__ import annotations

import sqlalchemy
from google.cloud.sql.connector import Connector

from .config import get_settings

_connector: Connector | None = None
_engine: sqlalchemy.engine.Engine | None = None


def _get_connector() -> Connector:
    global _connector
    if _connector is None:
        _connector = Connector()
    return _connector


def _make_connection():
    settings = get_settings()
    return _get_connector().connect(
        settings.instance_connection_name,
        "pg8000",
        user=settings.db_user,
        password=settings.db_pass,
        db=settings.db_name,
        enable_iam_auth=settings.db_iam_auth,
    )


def get_engine() -> sqlalchemy.engine.Engine:
    global _engine
    if _engine is None:
        _engine = sqlalchemy.create_engine(
            "postgresql+pg8000://",
            creator=_make_connection,
            pool_size=5,
            max_overflow=2,
            pool_timeout=30,
            pool_recycle=1800,
        )
    return _engine


def dispose_engine() -> None:
    """Releases pooled connections and closes the connector. Used on
    worker shutdown and between tests."""
    global _engine, _connector
    if _engine is not None:
        _engine.dispose()
        _engine = None
    if _connector is not None:
        _connector.close()
        _connector = None
