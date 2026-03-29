import { useEffect, useState } from 'react';
import { Link, useSearchParams } from 'react-router-dom';
import AOS from 'aos';
import api from '../api/api';
import Pagination from '../components/Pagination';

const STATUS_COLORS = {
  pending: 'warning', confirmed: 'info', processing: 'info',
  shipped: 'primary', delivered: 'success', cancelled: 'danger', returned: 'secondary',
};

export default function Orders() {
  const [orders, setOrders] = useState([]);
  const [pagination, setPagination] = useState({});
  const [loading, setLoading] = useState(true);
  const [searchParams, setSearchParams] = useSearchParams();
  const page = parseInt(searchParams.get('page') || '1');

  useEffect(() => {
    AOS.refresh();
    setLoading(true);
    api.get(`/orders.php?page=${page}&limit=10`)
      .then(r => {
        setOrders(r.data.data?.orders || []);
        setPagination(r.data.data?.pagination || {});
      })
      .catch(() => {})
      .finally(() => setLoading(false));
  }, [page]);

  return (
    <div className="container py-5">
      <h2 className="fw-bold mb-4" data-aos="fade-up">My Orders</h2>
      {loading ? (
        <div className="text-center py-5"><div className="spinner-border text-primary"></div></div>
      ) : orders.length === 0 ? (
        <div className="text-center py-5 text-muted">
          <i className="fas fa-box-open fa-4x mb-3"></i>
          <p>No orders yet.</p>
          <Link to="/products" className="btn btn-primary">Start Shopping</Link>
        </div>
      ) : (
        <>
          <div className="row g-3">
            {orders.map(order => (
              <div key={order.id} className="col-12" data-aos="fade-up">
                <div className="card border-0 shadow-sm">
                  <div className="card-body">
                    <div className="d-flex flex-wrap justify-content-between align-items-start gap-2">
                      <div>
                        <h6 className="fw-bold mb-1">{order.order_number}</h6>
                        <small className="text-muted">
                          {new Date(order.created_at).toLocaleDateString('en-BD', { year: 'numeric', month: 'short', day: 'numeric' })}
                          {' · '}{order.item_count} item(s)
                        </small>
                      </div>
                      <div className="d-flex gap-2 align-items-center">
                        <span className={`badge bg-${STATUS_COLORS[order.order_status] || 'secondary'}`}>
                          {order.order_status}
                        </span>
                        <span className={`badge bg-${order.payment_status === 'paid' ? 'success' : 'warning'} text-dark`}>
                          {order.payment_status}
                        </span>
                      </div>
                    </div>
                    <div className="d-flex justify-content-between align-items-center mt-2">
                      <div>
                        <span className="fw-bold text-primary fs-5">৳{parseFloat(order.total_amount).toLocaleString()}</span>
                        <small className="text-muted ms-2 text-capitalize">{order.payment_method}</small>
                      </div>
                      <Link to={`/orders/${order.id}`} className="btn btn-outline-primary btn-sm">
                        View Details
                      </Link>
                    </div>
                  </div>
                </div>
              </div>
            ))}
          </div>
          <Pagination
            page={pagination.page}
            totalPages={pagination.total_pages}
            onPageChange={p => setSearchParams({ page: p })}
          />
        </>
      )}
    </div>
  );
}
