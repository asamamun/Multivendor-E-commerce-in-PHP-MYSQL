import { Link } from 'react-router-dom';

export default function Footer() {
  return (
    <footer className="bg-dark text-light py-4 mt-5">
      <div className="container">
        <div className="row">
          <div className="col-md-4 mb-3">
            <h5 className="fw-bold"><i className="fas fa-store me-2"></i>MarketPlace</h5>
            <p className="text-muted small">Your one-stop multivendor shopping destination.</p>
          </div>
          <div className="col-md-4 mb-3">
            <h6 className="fw-semibold">Quick Links</h6>
            <ul className="list-unstyled small">
              <li><Link to="/products" className="text-muted text-decoration-none">Products</Link></li>
              <li><Link to="/shops" className="text-muted text-decoration-none">Shops</Link></li>
              <li><Link to="/cart" className="text-muted text-decoration-none">Cart</Link></li>
            </ul>
          </div>
          <div className="col-md-4 mb-3">
            <h6 className="fw-semibold">Account</h6>
            <ul className="list-unstyled small">
              <li><Link to="/login" className="text-muted text-decoration-none">Login</Link></li>
              <li><Link to="/register" className="text-muted text-decoration-none">Register</Link></li>
              <li><Link to="/orders" className="text-muted text-decoration-none">My Orders</Link></li>
            </ul>
          </div>
        </div>
        <hr className="border-secondary" />
        <p className="text-center text-muted small mb-0">© {new Date().getFullYear()} MarketPlace. All rights reserved.</p>
      </div>
    </footer>
  );
}
