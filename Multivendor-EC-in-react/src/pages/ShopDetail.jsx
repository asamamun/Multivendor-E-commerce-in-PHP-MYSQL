import { useEffect, useState } from 'react';
import { useParams, Link } from 'react-router-dom';
import AOS from 'aos';
import api from '../api/api';
import ProductCard from '../components/ProductCard';

export default function ShopDetail() {
  const { id } = useParams();
  const [shop, setShop] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    AOS.refresh();
    setLoading(true);
    api.get(`/shops.php?id=${id}`)
      .then(r => setShop(r.data.data?.shop || null))
      .catch(() => {})
      .finally(() => setLoading(false));
  }, [id]);

  if (loading) return <div className="text-center py-5"><div className="spinner-border text-primary"></div></div>;
  if (!shop) return <div className="container py-5 text-center text-muted">Shop not found.</div>;

  return (
    <div className="container py-5">
      {/* Shop Header */}
      <div className="card border-0 shadow-sm mb-4 p-4" data-aos="fade-up">
        <div className="d-flex align-items-center gap-4">
          <div className="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white"
            style={{ width: 80, height: 80, fontSize: '2rem' }}>
            <i className="fas fa-store"></i>
          </div>
          <div>
            <h3 className="fw-bold mb-1">{shop.vendor_name}</h3>
            <p className="text-muted mb-1"><i className="fas fa-envelope me-1"></i>{shop.email}</p>
            {shop.phone && <p className="text-muted mb-0"><i className="fas fa-phone me-1"></i>{shop.phone}</p>}
          </div>
        </div>
      </div>

      {/* Products */}
      <div className="d-flex justify-content-between align-items-center mb-3" data-aos="fade-up">
        <h5 className="fw-bold mb-0">Products from this shop</h5>
        <Link to={`/products?vendor=${id}`} className="btn btn-outline-primary btn-sm">View All</Link>
      </div>

      {shop.products?.length > 0 ? (
        <div className="row g-3">
          {shop.products.map(p => (
            <div key={p.id} className="col-6 col-md-4 col-lg-3">
              <ProductCard product={{ ...p, vendor_name: shop.vendor_name }} />
            </div>
          ))}
        </div>
      ) : (
        <div className="text-center py-5 text-muted">
          <i className="fas fa-box-open fa-3x mb-3"></i>
          <p>No products in this shop yet.</p>
        </div>
      )}
    </div>
  );
}
