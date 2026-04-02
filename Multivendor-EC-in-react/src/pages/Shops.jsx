import { useEffect, useState, useMemo } from 'react';
import { Link, useSearchParams } from 'react-router-dom';
import AOS from 'aos';
import api from '../api/api';
import Pagination from '../components/Pagination';
import { imgUrl } from '../config';

export default function Shops() {
  const [shops, setShops] = useState([]);
  const [pagination, setPagination] = useState({});
  const [loading, setLoading] = useState(true);
  const [searchParams, setSearchParams] = useSearchParams();

  const page = parseInt(searchParams.get('page') || '1');
  const search = searchParams.get('search') || '';

  useEffect(() => {
    AOS.refresh();
    setLoading(true);
    const params = new URLSearchParams({ page, limit: 12 });
    if (search) params.set('search', search);
    api.get(`/shops.php?${params}`)
      .then(r => {
        setShops(r.data.data?.shops || []);
        setPagination(r.data.data?.pagination || {});
      })
      .catch(() => {})
      .finally(() => setLoading(false));
  }, [page, search]);

  const sortedShops = useMemo(() => {
    return [...shops].sort((a, b) => a.vendor_id - b.vendor_id);
  }, [shops]);

  const handleSearch = (e) => {
    e.preventDefault();
    const q = e.target.q.value.trim();
    setSearchParams(q ? { search: q, page: 1 } : { page: 1 });
  };

  return (
    <div className="container py-5">
      <div className="d-flex flex-wrap justify-content-between align-items-center mb-4" data-aos="fade-up">
        <h2 className="fw-bold mb-2">Vendor Shops</h2>
        <form onSubmit={handleSearch} className="d-flex gap-2">
          <input
            name="q"
            defaultValue={search}
            className="form-control"
            placeholder="Search shops..."
            style={{ width: '220px' }}
          />
          <button className="btn btn-primary" type="submit">
            <i className="fas fa-search"></i>
          </button>
        </form>
      </div>

      {loading ? (
        <div className="text-center py-5"><div className="spinner-border text-primary"></div></div>
      ) : (
        <>
          <div className="row g-3">
            {sortedShops.map(shop => (
              <div key={shop.vendor_id} className="col-sm-6 col-md-4 col-lg-3" data-aos="fade-up">
                <Link to={`/shops/${shop.vendor_id}`} className="text-decoration-none">
                  <div className="card h-100 border-0 shadow-sm text-center p-3 shop-card">
                    <div className="mx-auto mb-3 rounded-circle overflow-hidden bg-light d-flex align-items-center justify-content-center"
                      style={{ width: 80, height: 80 }}>
                      {shop.sample_image ? (
                        <img
                          src={imgUrl(shop.sample_image)}
                          style={{ width: 80, height: 80, objectFit: 'cover' }}
                          alt={shop.vendor_name}
                          onError={e => { e.target.style.display = 'none'; }}
                        />
                      ) : (
                        <i className="fas fa-store fa-2x text-primary"></i>
                      )}
                    </div>
                    <h6 className="fw-bold text-dark">{shop.vendor_name}</h6>
                    <small className="text-muted">
                      <i className="fas fa-box me-1"></i>{shop.product_count} products
                    </small>
                    <div className="mt-2">
                      <span className="btn btn-outline-primary btn-sm">Visit Shop</span>
                    </div>
                  </div>
                </Link>
              </div>
            ))}
            {sortedShops.length === 0 && (
              <div className="col-12 text-center py-5 text-muted">
                <i className="fas fa-store-slash fa-3x mb-3"></i>
                <p>No shops found.</p>
              </div>
            )}
          </div>
          <Pagination
            page={pagination.page}
            totalPages={pagination.total_pages}
            onPageChange={p => setSearchParams({ page: p, ...(search ? { search } : {}) })}
          />
        </>
      )}
    </div>
  );
}
