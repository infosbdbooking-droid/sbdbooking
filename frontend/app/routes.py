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

    # Normalize JSON fields (If they are dicts, convert to values list)
    for field in ["recent_reviews", "trip_policies", "why_book_us", "booking_includes"]:
        val = safe_json(car.get(field), [])
        if isinstance(val, dict):
            car[field] = list(val.values())
        else:
            car[field] = val

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


@app.route('/proxy/filter-cars')
def filter_cars():
    try:
        params = request.args.to_dict()
        response = requests.get(f"{API_BASE_URL}/car-filter", params=params, timeout=10)
        return jsonify(response.json())
    except Exception as e:
        print("FILTER API ERROR:", e)
        return jsonify({"status": 0, "message": str(e)}), 500


# ======================
# BOOKINGS PAGE
# ======================
@app.route('/bookings')
def bookings():
    return render_template('bookings/index.html', api_base_url=API_BASE_URL)


# ======================
# ABOUT US PAGE
# ======================
@app.route('/about')
def about():
    return render_template('about/index.html', title="About Us | SBD Tour and Travels", api_base_url=API_BASE_URL)


# ======================
# HELP / FAQ PAGE
# ======================
@app.route('/help')
def help_page():
    return render_template('help/index.html', title="Help & Support | SBD Tour and Travels", api_base_url=API_BASE_URL)


# ======================
# CONTACT US PAGE
# ======================
@app.route('/contact')
def contact():
    return render_template('contact/index.html', title="Contact Us | SBD Tour and Travels", api_base_url=API_BASE_URL)


# ======================
# CONTACT API PROXY (Public)
# ======================
@app.route('/proxy/contact', methods=['POST'])
def contact_api_proxy():
    try:
        payload = request.get_json()
        if not payload:
            return jsonify({"status": 0, "message": "Invalid request data"}), 400

        api_url = f"{API_BASE_URL}/contact"
        response = requests.post(api_url, json=payload, timeout=10)

        try:
            data = response.json()
        except ValueError:
            print("CONTACT API PROXY ERROR: non-json response", response.status_code, response.text)
            return jsonify({
                "status": 0,
                "message": "Invalid response from API",
                "debug": response.text[:500]
            }), 502

        return jsonify(data), response.status_code

    except Exception as e:
        print("CONTACT API PROXY ERROR:", e)
        return jsonify({"status": 0, "message": "Failed to submit contact message"}), 500


# ======================
# MY ORDERS PROXY (Authenticated)
# ======================
@app.route('/proxy/my-orders')
def my_orders():
    try:
        headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
        auth_header = request.headers.get('Authorization')
        if auth_header:
            headers['Authorization'] = auth_header

        params = request.args.to_dict()
        api_url = f"{API_BASE_URL}/cab-orders"

        response = requests.get(api_url, headers=headers, params=params, cookies=request.cookies, timeout=15)

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
        print("MY ORDERS PROXY ERROR:", e)
        return jsonify({"status": 0, "message": "Failed to fetch orders"}), 500


# ======================
# CANCEL ORDER PROXY (Authenticated)
# ======================
@app.route('/proxy/cancel-order/<order_number>', methods=['POST'])
def cancel_order(order_number):
    try:
        headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
        auth_header = request.headers.get('Authorization')
        if auth_header:
            headers['Authorization'] = auth_header

        api_url = f"{API_BASE_URL}/cab-orders/{order_number}/cancel"
        response = requests.post(api_url, headers=headers, cookies=request.cookies, timeout=15)

        try:
            data = response.json()
        except ValueError:
            return jsonify({
                "status": 0,
                "message": "Invalid response from API"
            }), 502

        return jsonify(data), response.status_code

    except Exception as e:
        print("CANCEL ORDER PROXY ERROR:", e)
        return jsonify({"status": 0, "message": "Failed to cancel order"}), 500


# ======================
# ORDER DETAIL PROXY (Public)
# ======================
@app.route('/proxy/order-detail/<order_number>')
def order_detail_proxy(order_number):
    try:
        headers = {'Content-Type': 'application/json'}
        auth_header = request.headers.get('Authorization')
        if auth_header:
            headers['Authorization'] = auth_header

        api_url = f"{API_BASE_URL}/cab-orders/{order_number}"
        response = requests.get(api_url, headers=headers, timeout=10)

        try:
            data = response.json()
        except ValueError:
            return jsonify({"status": 0, "message": "Invalid response"}), 502

        return jsonify(data), response.status_code

    except Exception as e:
        print("ORDER DETAIL PROXY ERROR:", e)
        return jsonify({"status": 0, "message": "Failed to fetch order"}), 500


# ======================
# BLOGS SECTION
# ======================
@app.route('/blogs')
def blogs_index():
    page = request.args.get('page', 1, type=int)
    category_slug = request.args.get('category', '')
    search = request.args.get('search', '')
    
    blogs_data = {}
    categories = []
    
    try:
        # Fetch active blogs
        params = {
            'page': page,
            'category': category_slug,
            'search': search
        }
        res_blogs = requests.get(f"{API_BASE_URL}/blogs", params=params, timeout=10)
        if res_blogs.status_code == 200:
            blogs_data = res_blogs.json().get('data', {})
        
        # Fetch categories for widget
        res_cats = requests.get(f"{API_BASE_URL}/blogs/categories", timeout=10)
        if res_cats.status_code == 200:
            categories = res_cats.json().get('data', [])
            
    except Exception as e:
        print("FRONTEND BLOGS LIST ERROR:", e)
        
    return render_template(
        'blogs/index.html',
        blogs_data=blogs_data,
        categories=categories,
        search=search,
        active_category_slug=category_slug,
        api_base_url=API_BASE_URL
    )

@app.route('/blogs/<slug>')
def blog_detail(slug):
    blog = None
    comments = []
    tags = []
    related = []
    categories = []
    
    try:
        # Fetch blog details
        res_blog = requests.get(f"{API_BASE_URL}/blogs/{slug}", timeout=10)
        if res_blog.status_code == 200:
            payload = res_blog.json().get('data', {})
            blog = payload.get('blog')
            comments = payload.get('comments', [])
            tags = payload.get('tags', [])
            related = payload.get('related', [])
            
        # Fetch categories for sidebar widget
        res_cats = requests.get(f"{API_BASE_URL}/blogs/categories", timeout=10)
        if res_cats.status_code == 200:
            categories = res_cats.json().get('data', [])
            
    except Exception as e:
        print("FRONTEND BLOG DETAIL ERROR:", e)
        
    if not blog:
        return render_template('404.html', api_base_url=API_BASE_URL), 404
        
    return render_template(
        'blogs/detail.html',
        blog=blog,
        comments=comments,
        tags=tags,
        related=related,
        categories=categories,
        api_base_url=API_BASE_URL
    )

@app.route('/proxy/blogs/<int:blog_id>/comment', methods=['POST'])
def submit_blog_comment(blog_id):
    try:
        payload = request.get_json()
        if not payload:
            return jsonify({"status": 0, "message": "Invalid request data"}), 400
            
        res = requests.post(f"{API_BASE_URL}/blogs/{blog_id}/comment", json=payload, timeout=10)
        try:
            data = res.json()
        except ValueError:
            return jsonify({"status": 0, "message": "Invalid API response"}), 502
            
        return jsonify(data), res.status_code
        
    except Exception as e:
        print("COMMENT PROXY ERROR:", e)
        return jsonify({"status": 0, "message": "Failed to submit comment"}), 500


# ======================
# SEO LANDING PAGES SECTION
# ======================
@app.route('/page/<slug>')
def seo_page_detail(slug):
    page = None
    extended_data = {}
    related_blogs = []
    related_pages = []
    related_routes = []
    categories = []
    
    try:
        # Fetch SEO page details
        res = requests.get(f"{API_BASE_URL}/seo-page/{slug}", timeout=10)
        if res.status_code == 200:
            payload = res.json().get('data', {})
            page = payload.get('page')
            extended_data = payload.get('extended_data', {})
            related_blogs = payload.get('related_blogs', [])
            related_pages = payload.get('related_pages', [])
            related_routes = payload.get('related_routes', [])
            categories = payload.get('categories', [])
            
    except Exception as e:
        print("FRONTEND SEO PAGE DETAIL ERROR:", e)
        
    if not page:
        return render_template('404.html', api_base_url=API_BASE_URL), 404
        
    title = page.get('meta_title') or page.get('title') if page else None
    meta_description = page.get('meta_description') or page.get('short_description') if page else None
    meta_keywords = page.get('meta_keywords') if page else None
    canonical_url = page.get('canonical_url') if page else None
    schema_type = page.get('schema_type') if page else None

    return render_template(
        'seo/detail.html',
        page=page,
        extended_data=extended_data,
        related_blogs=related_blogs,
        related_pages=related_pages,
        related_routes=related_routes,
        categories=categories,
        title=title,
        meta_description=meta_description,
        meta_keywords=meta_keywords,
        canonical_url=canonical_url,
        schema_type=schema_type,
        api_base_url=API_BASE_URL
    )


# ======================
# 404 PAGE
# ======================
@app.errorhandler(404)
def not_found(e):
    return render_template('404.html', api_base_url=API_BASE_URL), 404
