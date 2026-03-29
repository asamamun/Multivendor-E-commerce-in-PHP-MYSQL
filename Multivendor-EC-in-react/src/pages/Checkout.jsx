import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { toast } from 'react-toastify';
import { useCart } from '../context/CartContext';
import { useAuth } from '../context/AuthContext';
import { imgUrl } from '../config';
import api from '../api/api';

const PAYMENT_METHODS = ['cod', 'bkash', 'nagad', 'rocket'];

export default function Checkout() {
  const { cart, total, clearCart } = useCart();
  const { user } = useAuth();
  const navigate = useNavigate();
  const [loading, setLoading] = useState(false);
  const [form, setForm] = useState({
    name: user?.name || '',
    phone: user?.phone || '',
    address: '',
    city: '',
    zip: '',
    payment_method: 'cod',
    notes: '',
  });

  const handleSubmit = async (e) => {
    e.preventDefault();
    if (cart.length === 0) { toast.error('Cart is empty'); return; }
    setLoading(true);
    try {
      const payload = {
        items: cart.map(i => ({ product_id: i.id, quantity: i.qty })),
        shipping_address: {
          name: form.name,
          phone: form.phone,
          address: form.address,
          city: form.city,
          zip: form.zip,
        },
        payment_method: form.payment_method,
        notes: form.notes,
      };
      const res = await api.post('/orders.php', payload);
      const { order_number } = res.data.data;
      clearCart();
      toast.success(`Order ${order_number} placed successfully!`);
      navigate('/orders');
    } catch (err) {
      toast.error(err.response?.data?.message || 'Failed to place order');
    } finally {
      setLoading(false);
    }
  };

  const f = (k) => ({ value: form[k], onChange: e => setForm({ ...form, [k]: e.target.value }) });

  return (
    <div className="container py-5">
      <h2 className="fw-bold mb-4" data-aos="fade-up">Checkout</h2>
      <form onSubmit={handleSubmit}>
        <div className="row g-4">
          {/* Shipping */}
          <div className="col-lg-7">
            <div className="card border-0 shadow-sm mb-4" data-aos="fade-up">
              <div className="card-body">
                <h5 className="fw-bold mb-3"><i className="fas fa-map-marker-alt me-2 text-primary"></i>Shipping Address</h5>
                <div className="row g-3">
                  <div className="col-md-6">
                    <label className="form-label">Full Name</label>
                    <input className="form-control" {...f('name')} required />
                  </div>
                  <div className="col-md-6">
                    <label className="form-label">Phone</label>
                    <input className="form-control" {...f('phone')} required />
                  </div>
                  <div className="col-12">
                    <label className="form-label">Address</label>
                    <input className="form-control" {...f('address')} required />
                  </div>
                  <div className="col-md-6">
                    <label className="form-label">City</label>
                    <input className="form-control" {...f('city')} required />
                  </div>
                  <div className="col-md-6">
                    <label className="form-label">ZIP Code</label>
                    <input className="form-control" {...f('zip')} />
                  </div>
                  <div className="col-12">
                    <label className="form-label">Order Notes (optional)</label>
                    <textarea className="form-control" rows={2} {...f('notes')} />
                  </div>
                </div>
              </div>
            </div>

            {/* Payment */}
            <div className="card border-0 shadow-sm" data-aos="fade-up">
              <div className="card-body">
                <h5 className="fw-bold mb-3"><i className="fas fa-credit-card me-2 text-primary"></i>Payment Method</h5>
                <div className="row g-2">
                  {PAYMENT_METHODS.map(m => (
                    <div key={m} className="col-6 col-md-3">
                      <label className={`d-block border rounded p-3 text-center cursor-pointer ${form.payment_method === m ? 'border-primary bg-primary bg-opacity-10' : ''}`}
                        style={{ cursor: 'pointer' }}>
                        <input
                          type="radio"
                          name="payment"
                          value={m}
                          checked={form.payment_method === m}
                          onChange={() => setForm({ ...form, payment_method: m })}
                          className="d-none"
                        />
                        <div className="fw-semibold text-capitalize">{m === 'cod' ? 'Cash on Delivery' : m.charAt(0).toUpperCase() + m.slice(1)}</div>
                      </label>
                    </div>
                  ))}
                </div>
              </div>
            </div>
          </div>

          {/* Summary */}
          <div className="col-lg-5">
            <div className="card border-0 shadow-sm" data-aos="fade-left">
              <div className="card-body">
                <h5 className="fw-bold mb-3">Order Summary</h5>
                {cart.map(item => (
                  <div key={item.id} className="d-flex align-items-center gap-2 mb-2">
                    <img
                      src={imgUrl(item.image)}
                      style={{ width: 50, height: 50, objectFit: 'cover', borderRadius: 6 }}
                      alt={item.name}
                      onError={e => { e.target.src = 'https://placehold.co/50x50?text=img'; }}
                    />
                    <div className="flex-grow-1">
                      <div className="small fw-semibold text-truncate" style={{ maxWidth: 160 }}>{item.name}</div>
                      <small className="text-muted">x{item.qty}</small>
                    </div>
                    <span className="fw-semibold">৳{(item.price * item.qty).toLocaleString()}</span>
                  </div>
                ))}
                <hr />
                <div className="d-flex justify-content-between mb-1">
                  <span>Subtotal</span><span>৳{total.toLocaleString()}</span>
                </div>
                <div className="d-flex justify-content-between mb-2">
                  <span>Shipping</span><span>৳60</span>
                </div>
                <div className="d-flex justify-content-between fw-bold fs-5 mb-3">
                  <span>Total</span>
                  <span className="text-primary">৳{(total + 60).toLocaleString()}</span>
                </div>
                <button className="btn btn-primary w-100 btn-lg" type="submit" disabled={loading}>
                  {loading ? <span className="spinner-border spinner-border-sm me-2"></span> : <i className="fas fa-check-circle me-2"></i>}
                  Place Order
                </button>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  );
}
