import { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import AOS from 'aos';
import { useAuth } from '../context/AuthContext';
import { useCart } from '../context/CartContext';
import api from '../api/api';

export default function Dashboard() {
  const { user } = useAuth();
  const { count, total } = useCart();
  const [orders, setOrders] = useState([]);
  const [stats, setStats] = useState({ total: 0, delivered: 0, pending: 0 });
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    AOS.refresh();
    api.get('/orders.php?limit=5')
      .then(r => {
        const list = r.data.data?.orders || [];
        setOrders(list);
        const pagination = r.data.data?.pagination || {};
        setStats({
          total: pagination.total || 0,
          delivered: list.filter(o => o.order_status === 'delivered').length,
          pending: list.filter(o => o.order_status === 'pending').length,
        });
      })
      .catch(() => {})
      .finally(() => setLoading(false));
  }, []);

  const STATUS_COLORS = {
    pending: 'warning', confirmed: 'info', processing: 'info',
    shipped: 'primary', delivered: 'success', cancelled: 'danger', returned: 'secondary',
  };

  return (
    <div className="container py-5">
      {/* Welcome */}
      <div className="card border-0 shadow-sm bg-primary text-white mb-4 p-4" data-aos="fade-up">
        <div className="d-flex align-items-center gap-3">
          <div className="rounded-circle bg-white d-flex align-items-center justify-content-center"
            style={{ width: 60, height: 60 }}>
            <i className="fas fa-user fa-2x text-primary"></i>
          </div>
          <div>
            <h4 className="fw-bold mb-0">Welcome, {user?.name}!</h4>
            <small className="opacity-75">{user?.email}</small>
          </div>
        </div>
      </div>

      {/* Stats */}
      <div className="row g-3 mb-4">
        {[
          { label: 'Total Orders', value: stats.total, icon: 'fa-box', color: 'primary' },
          { label: 'Cart Items', value: count, icon: 'fa-shopping-cart', color: 'success' },
          { label: 'Cart Value', value: `৳${total.toLocaleString()}`, icon: 'fa-wallet', color: 'warning' },
        ].map((s, i) => (
          <div key={i} className="col-sm-4" data-aos="zoom-in" data-aos-delay={i * 100}>
            <div className={`card border-0 shadow-sm border-start border-${s.color} border-4`}>
              <div className="card-body d-flex align-items-center gap-3">
                <i className={`fas ${s.icon} fa-2x text-${s.color}`}></i>
                <div>
                  <div className="fw-bold fs-4">{s.value}</div>
                  <small className="text-muted">{s.label}</small>
                </div>
              </div>
            </div>
          </div>
        ))}
      </div>

      {/* Quick Links */}
      <div className="row g-3 mb-4">
        {[
          { to: '/products', icon: 'fa-shopping-bag', label: 'Browse Products', color: 'primary' },
          { to: '/shops', icon: 'fa-store', label: 'Browse Shops', color: 'success' },
          { to: '/cart', icon: 'fa-shopping-cart', label: 'View Cart', color: 'warning' },
          { to: '/orders', icon: 'fa-list', label: 'All Orders', color: 'info' },
        ].map((l, i) => (
          <div key={i} className="col-6 col-md-3" data-aos="fade-up" data-aos-delay={i * 80}>
            <Link to={l.to} className="text-decoration-none">
              <div className="card border-0 shadow-sm text-center p-3 h-100">
                <i className={`fas ${l.icon} fa-2x text-${l.color} mb-2`}></i>
                <small className="fw-semibold text-dark">{l.label}</small>
              </div>
            </Link>
          </div>
        ))}
      </div>

      {/* Recent Orders */}
      <div className="card border-0 shadow-sm" data-aos="fade-up">
        <div className="card-body">
          <div className="d-flex justify-content-between align-items-center mb-3">
            <h6 className="fw-bold mb-0">Recent Orders</h6>
            <Link to="/orders" className="btn btn-outline-primary btn-sm">View All</Link>
          </div>
          {loading ? (
            <div className="text-center py-3"><div className="spinner-border spinner-border-sm text-primary"></div></div>
          ) : orders.length === 0 ? (
            <p className="text-muted text-center py-3">No orders yet.</p>
          ) : (
            <div className="table-responsive">
              <table className="table table-hover align-middle mb-0">
                <thead className="table-light">
                  <tr>
                    <th>Order #</th>
                    <th>Date</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                  {orders.map(o => (
                    <tr key={o.id}>
                      <td className="fw-semibold">{o.order_number}</td>
                      <td><small>{new Date(o.created_at).toLocaleDateString()}</small></td>
                      <td className="text-primary fw-semibold">৳{parseFloat(o.total_amount).toLocaleString()}</td>
                      <td>
                        <span className={`badge bg-${STATUS_COLORS[o.order_status] || 'secondary'}`}>
                          {o.order_status}
                        </span>
                      </td>
                      <td>
                        <Link to={`/orders/${o.id}`} className="btn btn-outline-primary btn-sm">View</Link>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          )}
        </div>
      </div>
    </div>
  );
}
