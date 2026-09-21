from flask import Blueprint, jsonify

from ..repositories.compound_repository import fetch_abbreviations

bp = Blueprint("abbreviations", __name__)


@bp.route("/abrev")
def get_abbreviations():
    return jsonify(fetch_abbreviations())
