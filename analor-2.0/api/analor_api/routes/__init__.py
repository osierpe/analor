from flask import Flask

from . import abbreviations, compounds


def register_routes(app: Flask) -> None:
    app.register_blueprint(abbreviations.bp)
    app.register_blueprint(compounds.bp)
