import { useEffect, useState } from 'react';
import { useSearchParams } from 'react-router-dom';
import AOS from 'aos';
import api from '../api/api';
import ProductCard from '../components/ProductCard';
import Pagination from '../components/Pagination';

export default function Products() {
  const [products, setProducts] = useState([]);
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
    api.get(`/products.php?${params}`)
      .then(r => {
        setProducts(r.data.data?.products || []);
        setPagination(r.data.data?.pagination || {});
      })
      .catch(() => {})
      .finally(() => setLoading(false));
  }, [page, search]);

  const handleSearch = (e) => {
    e.preventDefault();
    const q = e.target.q.value.trim();
    setSearchParams(q ? { search: q, page: 1 } : { page: 1 });
  };

  return (
    <div className="container py-5">
      <div className="d-flex flex-wrap justify-content-between align-items-center mb-4" data-aos="fade-up">
        <h2 className="fw-bold mb-2">All Products</h2>
        <form onSubmit={handleSearch} className="d-flex gap-2">
          <input
            name="q"
            defaultValue={search}
            className="form-control"
            placeholder="Search products..."
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
            {products.map(p => (
              <div key={p.id} className="col-6 col-md-4 col-lg-3">
                <ProductCard product={p} />
              </div>
            ))}
            {products.length === 0 && (
              <div className="col-12 text-center py-5 text-muted">
                <i className="fas fa-box-open fa-3x mb-3"></i>
                <p>No products found.</p>
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
