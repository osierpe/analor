"""WSGI entrypoint. Gunicorn/waitress point at `app:app`; the actual
application is assembled in the `analor_api` package."""
from analor_api import create_app

app = create_app()

if __name__ == "__main__":
    app.run(host="0.0.0.0", port=8080, debug=False)
