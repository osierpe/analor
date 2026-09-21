"""Application configuration loaded from environment variables.

No credential ever lives in source code: everything here comes from the
process environment, which on Cloud Run should be populated from Secret
Manager (for DB_PASS) and plain env vars / substitutions for the rest.
For local development, copy `.env.example` to `.env` and fill it in;
`load_dotenv()` picks it up automatically and is a no-op in production
where no `.env` file exists.
"""
from __future__ import annotations

import os
from dataclasses import dataclass
from functools import lru_cache

from dotenv import load_dotenv

load_dotenv()


class ConfigError(RuntimeError):
    """Raised when required configuration is missing or invalid."""


def _require(name: str) -> str:
    value = os.environ.get(name)
    if not value:
        raise ConfigError(f"Variável de ambiente obrigatória não definida: {name}")
    return value


@dataclass(frozen=True)
class Settings:
    instance_connection_name: str
    db_user: str
    db_pass: str | None
    db_name: str
    db_iam_auth: bool
    cors_origins: list[str]


@lru_cache
def get_settings() -> Settings:
    db_iam_auth = os.environ.get("DB_IAM_AUTH", "false").strip().lower() == "true"

    cors_origins = [
        origin.strip()
        for origin in _require("CORS_ORIGINS").split(",")
        if origin.strip()
    ]

    return Settings(
        instance_connection_name=_require("INSTANCE_CONNECTION_NAME"),
        db_user=_require("DB_USER"),
        # With IAM auth the Cloud SQL connector authenticates using the
        # service account's identity, so there is no password at all.
        db_pass=None if db_iam_auth else _require("DB_PASS"),
        db_name=os.environ.get("DB_NAME", "postgres"),
        db_iam_auth=db_iam_auth,
        cors_origins=cors_origins,
    )
