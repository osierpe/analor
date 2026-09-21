import logging

from flask import Flask
from flask_cors import CORS

from .config import get_settings
from .routes import register_routes


def create_app() -> Flask:
    settings = get_settings()

    _setup_logging()

    app = Flask(__name__)
    CORS(app, origins=settings.cors_origins)

    register_routes(app)

    return app


def _setup_logging() -> None:
    """Uses Cloud Logging when running on GCP; falls back to stdlib
    logging so the app also runs locally without GCP credentials."""
    try:
        import google.cloud.logging

        client = google.cloud.logging.Client()
        client.setup_logging()
    except Exception:
        logging.basicConfig(level=logging.INFO)
        logging.getLogger(__name__).info(
            "Cloud Logging indisponível; usando logging padrão."
        )
