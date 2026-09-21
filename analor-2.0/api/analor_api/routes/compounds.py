from flask import Blueprint, jsonify, request

from ..services.compound_search import InvalidSearchRequest, run_search

bp = Blueprint("compounds", __name__)


@bp.route("/search")
def search():
    try:
        results = run_search(request.args.get("data"))
    except InvalidSearchRequest as exc:
        return jsonify({"error": str(exc)}), 400

    return jsonify(results)
