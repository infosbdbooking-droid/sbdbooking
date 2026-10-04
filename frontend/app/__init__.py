import os
import time
from flask import Flask

app = Flask(__name__)
app.config['SECRET_KEY'] = 'your_secret_key'

@app.context_processor
def inject_asset_version():
    try:
        static_js = os.path.join(app.root_path, 'static', 'js', 'app.js')
        mtime = int(os.path.getmtime(static_js))
    except Exception:
        mtime = int(time.time())
    return dict(asset_version=mtime)

from app import routes
