import { useEffect, useState } from 'react';
import { useParams, Link } from 'react-router-dom';
import AOS from 'aos';
import api from '../api/api';
import { imgUrl } from '../config';

const STATUS_COLORS = {
  pending: 'warning', confirmed: 'info', processing: 'info',
  shipped: 'primary', delivered: 'success', cancelled: 'danger', returned: 'secondary',
};

const DELIVERY_COLORS = {
  assigned: 'secondary', picked_up: 'info', in_transit: 'primary',
  delivered: 'success', failed: 'danger', returned: 'warning',
};

export default function OrderDetail() {
  const { id } = useParams();
  const [order, setOrder] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    AOS.refresh();
    api.get(`/orders.php?id=${id}`)
      .then(r => setOrder(r.data.data?.order || null))
      .catch(() => {})
      .finally(() => setLoading(false));
  }, [id]);

  if (loading) return <div className="text-center py-5"><div className="spinner-border text-primary"></div></div>;
  if (!order) return <div className="container py-5 text-center text-muted">Order not found.</div>;

  const addr = order.shipping_address || {};

  return (
    <div className="container py-5">
      <div className="d-flex align-items-center gap-3 mb-4" data-aos="fade-up">
        <Link to="/orders" className="btn btn-outline-secondary btn-sm">
          <i className="fas fa-arrow-left me-1"></i>Back
        </Link>
        <h4 className="fw-bold mb-0">{order.order_number}</h4>
        <span className={`badge bg-${STATUS_COLORS[order.order_status] || 'secondary'}`}>{order.order_status}</span>
      </div>

      <div className="row g-4">
        <div className="col-lg-8">
          {/* Items */}
          <div className="card border-0 shadow-sm mb-4" data-aos="fade-up">
            <div className="card-body">
              <h6 className="fw-bold mb-3">Order Items</h6>
              {order.items?.map(item => (
                <div key={item.id} className="d-flex align-items-center gap-3 mb-3 pb-3 border-bottom">
                  <img
                    src={imgUrl(item.product_image)}
                    style={{ width: 60, height: 60, objectFit: 'cover', borderRadius: 8 }}
                    alt={item.product_name}
                    onError={e => { e.target.src = 'https://placehold.co/60x60?text=img'; }}
                  />
                  <div className="flex-grow-1">
                    <div className="fw-semibold">{item.product_name}</div>
                    <small className="text-muted">SKU: {item.product_sku} · Qty: {item.quantity}</small>
                  </div>
                  <div className="fw-bold text-primary">৳{parseFloat(item.total_price).toLocaleString()}</div>
                </div>
              ))}
            </div>
          </div>

          {/* Shipping */}
          <div className="card border-0 shadow-sm" data-aos="fade-up">
            <div className="card-body">
              <h6 className="fw-bold mb-3"><i className="fas fa-map-marker-alt me-2 text-primary"></i>Shipping Address</h6>
              <p className="mb-1 fw-semibold">{addr.name}</p>
              <p className="mb-1 text-muted">{addr.phone}</p>
              <p className="mb-1 text-muted">{addr.address}</p>
              <p className="mb-0 text-muted">{addr.city} {addr.zip}</p>
            </div>
          </div>
        </div>

        <div className="col-lg-4">
          {/* Summary */}
          <div className="card border-0 shadow-sm mb-4" data-aos="fade-left">
            <div className="card-body">
              <h6 className="fw-bold mb-3">Payment Summary</h6>
              <div className="d-flex justify-content-between mb-2">
                <span>Subtotal</span><span>৳{parseFloat(order.subtotal).toLocaleString()}</span>
              </div>
              <div className="d-flex justify-content-between mb-2">
                <span>Shipping</span><span>৳{parseFloat(order.shipping_cost).toLocaleString()}</span>
              </div>
              <hr />
              <div className="d-flex justify-content-between fw-bold">
                <span>Total</span>
                <span className="text-primary">৳{parseFloat(order.total_amount).toLocaleString()}</span>
              </div>
              <hr />
              <div className="d-flex justify-content-between">
                <span>Payment</span>
                <span className="text-capitalize fw-semibold">{order.payment_method}</span>
              </div>
              <div className="d-flex justify-content-between mt-1">
                <span>Status</span>
                <span className={`badge bg-${order.payment_status === 'paid' ? 'success' : 'warning'} text-dark`}>
                  {order.payment_status}
                </span>
              </div>
            </div>
          </div>

          {/* Delivery */}
          {order.delivery_status && (
            <div className="card border-0 shadow-sm" data-aos="fade-left">
              <div className="card-body">
                <h6 className="fw-bold mb-3"><i className="fas fa-truck me-2 text-primary"></i>Delivery</h6>
                <span className={`badge bg-${DELIVERY_COLORS[order.delivery_status] || 'secondary'}`}>
                  {order.delivery_status}
                </span>
                {order.tracking_number && (
                  <p className="mt-2 mb-0 small text-muted">Tracking: {order.tracking_number}</p>
                )}
              </div>
            </div>
          )}
        </div>
      </div>
    </div>
  );
}
