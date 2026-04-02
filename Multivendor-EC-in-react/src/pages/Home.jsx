import { useEffect, useState, useMemo } from 'react';
import { Link } from 'react-router-dom';
import AOS from 'aos';
import api from '../api/api';
import ProductCard from '../components/ProductCard';

export default function Home() {
  const [featured, setFeatured] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    AOS.refresh();
    api.get('/products.php?featured=1&limit=8')
      .then(r => setFeatured(r.data.data?.products || []))
      .catch(() => {})
      .finally(() => setLoading(false));
  }, []);

  const sortedFeatured = useMemo(() => {
    return [...featured].sort((a, b) => a.id - b.id);
  }, [featured]);

  return (
    <>
      {/* Hero */}
      <section className="bg-primary text-white py-5">
        <div className="container py-4">
          <div className="row align-items-center">
            <div className="col-lg-6" data-aos="fade-right">
              <h1 className="display-4 fw-bold mb-3">Shop from the Best Vendors</h1>
              <p className="lead mb-4">Discover thousands of products from verified sellers across Bangladesh.</p>
              <div className="d-flex gap-3">
                <Link to="/products" className="btn btn-light btn-lg text-primary fw-semibold">
                  <i className="fas fa-shopping-bag me-2"></i>Shop Now
                </Link>
                <Link to="/shops" className="btn btn-outline-light btn-lg">
                  <i className="fas fa-store me-2"></i>Browse Shops
                </Link>
              </div>
            </div>
            <div className="col-lg-6 text-center mt-4 mt-lg-0" data-aos="fade-left">
              <i className="fas fa-shopping-cart" style={{ fontSize: '10rem', opacity: 0.3 }}></i>
            </div>
          </div>
        </div>
      </section>

      {/* Features */}
      <section className="py-4 bg-light">
        <div className="container">
          <div className="row g-3 text-center">
            {[
              { icon: 'fa-truck', title: 'Fast Delivery', desc: 'Delivered to your door' },
              { icon: 'fa-shield-alt', title: 'Secure Payment', desc: 'bKash, Nagad, COD' },
              { icon: 'fa-undo', title: 'Easy Returns', desc: 'Hassle-free returns' },
              { icon: 'fa-headset', title: '24/7 Support', desc: 'Always here to help' },
            ].map((f, i) => (
              <div key={i} className="col-6 col-md-3" data-aos="zoom-in" data-aos-delay={i * 100}>
                <div className="p-3">
                  <i className={`fas ${f.icon} fa-2x text-primary mb-2`}></i>
                  <h6 className="fw-semibold">{f.title}</h6>
                  <small className="text-muted">{f.desc}</small>
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Featured Products */}
      <section className="py-5">
        <div className="container">
          <div className="d-flex justify-content-between align-items-center mb-4" data-aos="fade-up">
            <h2 className="fw-bold mb-0">Featured Products</h2>
            <Link to="/products" className="btn btn-outline-primary btn-sm">View All</Link>
          </div>
          {loading ? (
            <div className="text-center py-5"><div className="spinner-border text-primary"></div></div>
          ) : (
            <div className="row g-3">
              {sortedFeatured.map(p => (
                <div key={p.id} className="col-6 col-md-4 col-lg-3">
                  <ProductCard product={p} />
                </div>
              ))}
              {sortedFeatured.length === 0 && (
                <p className="text-muted text-center">No featured products yet.</p>
              )}
            </div>
          )}
        </div>
      </section>

      {/* CTA */}
      <section className="bg-primary text-white py-5" data-aos="fade-up">
        <div className="container text-center">
          <h3 className="fw-bold mb-3">Are you a Vendor?</h3>
          <p className="mb-4">Join our marketplace and start selling to thousands of customers.</p>
          <Link to="/register" className="btn btn-light btn-lg text-primary fw-semibold">
            Register as Vendor
          </Link>
        </div>
      </section>
    </>
  );
}
