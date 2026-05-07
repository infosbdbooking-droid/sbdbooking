from app import app
from flask import render_template, request, jsonify
import requests
import json
from config import API_BASE_URL


# ======================
# HOME PAGE
# ======================
@app.route('/')
def index():
    cars = []
    sliders = []
    try:
        # Fetch Cars
        response_cars = requests.get(f"{API_BASE_URL}/cars", timeout=10)
        data_cars = response_cars.json()
        cars = data_cars.get("data", []) if data_cars.get("status") == 1 else []
        
        # Fetch Sliders
        response_sliders = requests.get(f"{API_BASE_URL}/sliders", timeout=10)
        data_sliders = response_sliders.json()
        # Slider API returns status, message, and data (which is a paginated object)
        if data_sliders.get("status") == 1:
            sliders_payload = data_sliders.get("data", {})
            sliders = sliders_payload.get("data", [])
            
    except Exception as e:
        print("HOME API ERROR:", e)

    return render_template('index.html', cars=cars, sliders=sliders, api_base_url=API_BASE_URL)



# ======================
# LOGIN / REGISTER (MOBILE + PASSWORD)
# ======================
@app.route('/login', methods=['POST'])
def login():
    try:
        payload = request.get_json()

        if not payload:
            return jsonify({
                "status": 0,
                "message": "Invalid request data"
            }), 400

        mobile = payload.get("mobile")
        password = payload.get("password")

        if not mobile or not password:
            return jsonify({
                "status": 0,
                "message": "Mobile and password are required"
            }), 422

        # 🔥 Laravel API call
        api_payload = {
            "mobile": mobile,
            "password": password
        }
        if payload.get("name"):
            api_payload["name"] = payload.get("name")

        response = requests.post(
            f"{API_BASE_URL}/loginOrRegister",
            json=api_payload,
            timeout=10
        )

        try:
            data = response.json()
        except ValueError:
            print("LOGIN API ERROR: non-json response", response.status_code, response.text)
            return jsonify({"status": 0, "message": "Invalid response from auth API"}), 502

        return jsonify(data), response.status_code

    except Exception as e:
        print("LOGIN API ERROR:", e)
        return jsonify({
            "status": 0,
            "message": "Login failed"
        }), 500




# ======================
# CAR DETAIL PAGE
# ======================
@app.route('/car-rental/<int:car_id>')
def car_detail(car_id):

    # 👉 Your backend WORKING API is POST /carDetails
    api_url = f"{API_BASE_URL}/carDetails"

    try:
        response = requests.post(api_url, json={"car_id": car_id}, timeout=10)
        data = response.json()

        if data.get("status") != 1 or not data.get("data"):
            return render_template(
                'car_rental/car_detail.html',
                car=None,
                error="Car not found.",
                api_base_url=API_BASE_URL
            ), 404

        car = data["data"]

    except Exception as e:
        print("CAR DETAIL API ERROR:", e)
        return render_template(
            'car_rental/car_detail.html',
            car=None,
            error="Failed to load car details.",
            api_base_url=API_BASE_URL
        ), 502

    # ======================
    # 🔥 JSON STRING → PYTHON OBJECT (IMPORTANT)
    # ======================
    def safe_json(value, default):
        try:
            if isinstance(value, str):
                return json.loads(value)
            return value if value is not None else default
        except Exception:
            return default

    car["booking_includes"] = safe_json(car.get("booking_includes"), [])
    car["why_book_us"] = safe_json(car.get("why_book_us"), [])
    car["trip_policies"] = safe_json(car.get("trip_policies"), [])
    car["recent_reviews"] = safe_json(car.get("recent_reviews"), [])

    # ======================
    # NORMALIZE SIMPLE FIELDS
    # ======================
    car["is_ac"] = int(car.get("is_ac", 0))
    car["rating_value"] = float(car.get("rating_value", 0))
    car["rating_count"] = int(car.get("rating_count", 0))
    car["min_trip_amount"] = float(car.get("min_trip_amount") or 0)

    # ✅ NO MEDIA_BASE_URL
    # API already gives full image URL
    # car["car_photos"] stays as-is

    return render_template(
        'car_rental/car_detail.html',
        car=car,
        api_base_url=API_BASE_URL
    )


# ======================
# CALCULATE CHARGES PROXY
# ======================
@app.route('/calculate-charges', methods=['POST'])
def calculate_charges():
    try:
        payload = request.get_json()
        if not payload:
            return jsonify({"status": 0, "message": "Invalid request data"}), 400

        api_url = f"{API_BASE_URL}/calculate-charges"
        response = requests.post(api_url, json=payload, timeout=10)
        
        try:
            data = response.json()
        except ValueError:
            return jsonify({
                "status": 0, 
                "message": "Invalid response from API",
                "debug": response.text[:500]
            }), 502

        return jsonify(data), response.status_code

    except Exception as e:
        print("CALCULATE CHARGES API ERROR:", e)
        return jsonify({"status": 0, "message": "Failed to calculate charges"}), 500


# ======================
# PLACE ORDER PROXY
# ======================
@app.route('/place-order', methods=['POST'])
def place_order():
    try:
        payload = request.get_json()
        if not payload:
            return jsonify({"status": 0, "message": "Invalid request data"}), 400

        api_url = f"{API_BASE_URL}/cab-orders"
        
        # Forward the bearer token if it exists (for logged-in users)
        headers = {'Content-Type': 'application/json'}
        auth_header = request.headers.get('Authorization')
        if auth_header:
            headers['Authorization'] = auth_header

        response = requests.post(api_url, json=payload, headers=headers, timeout=15)
        
        try:
            data = response.json()
        except ValueError:
            return jsonify({
                "status": 0, 
                "message": "Invalid response from API",
                "debug": response.text[:500]
            }), 502

        return jsonify(data), response.status_code

    except Exception as e:
        print("PLACE ORDER API ERROR:", e)
        return jsonify({"status": 0, "message": "Failed to place order"}), 500



# ======================
# CAR LISTING / SEARCH RESULTS
# ======================
@app.route('/search-results')
def search_results():
    cars = []
    car_types = []
    try:
        # Fetch search parameters
        pickup = request.args.get('pickup', '')
        destination = request.args.get('destination', '')
        date = request.args.get('date', '')
        pickup_time = request.args.get('pickup_time', '')
        selected_type = request.args.get('car_type_id', '')

        # Fetch Cars from Laravel API (with optional filter)
        car_api_url = f"{API_BASE_URL}/cars"
        if selected_type:
            car_api_url += f"?car_type_id={selected_type}"
            
        response = requests.get(car_api_url, timeout=10)
        data = response.json()
        
        if data.get("status") == 1:
            cars = data.get("data", [])

        # Fetch Car Types for sidebar filters
        type_response = requests.get(f"{API_BASE_URL}/car-types", timeout=10)
        type_data = type_response.json()
        if type_data.get("status") == 1:
            car_types = type_data.get("data", [])
            
    except Exception as e:
        print("SEARCH RESULTS API ERROR:", e)

    return render_template('car_listing/index.html', cars=cars, car_types=car_types, api_base_url=API_BASE_URL)


@app.route('/api/filter-cars')
def filter_cars():
    try:
        params = request.args.to_dict()
        response = requests.get(f"{API_BASE_URL}/car-filter", params=params, timeout=10)
        return jsonify(response.json())
    except Exception as e:
        print("FILTER API ERROR:", e)
        return jsonify({"status": 0, "message": str(e)}), 500


# ======================
# 404 PAGE
# ======================
@app.errorhandler(404)
def not_found(e):
    return render_template('404.html', api_base_url=API_BASE_URL), 404
