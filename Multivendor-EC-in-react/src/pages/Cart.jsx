import { Link, useNavigate } from 'react-router-dom';
import { useCart } from '../context/CartContext';
import { useAuth } from '../context/AuthContext';
import { imgUrl } from '../config';
import { toast } from 'react-toastify';
import { memo, useCallback } from 'react';

const CartItem = memo(({ item, onRemove, onUpdateQty }) => {
  return (
    <div key={item.id} className="card border-0 shadow-sm mb-3" data-aos="fade-up">
      <div className="card-body d-flex align-items-center gap-3">
        <img
          src={imgUrl(item.image)}
          style={{ width: 80, height: 80, objectFit: 'cover', borderRadius: 8 }}
          alt={item.name}
          onError={e => { e.target.src = 'https://placehold.co/80x80?text=img'; }}
        />
        <div className="flex-grow-1">
          <h6 className="fw-semibold mb-1">{item.name}</h6>
          <small className="text-muted">{item.vendor}</small>
          <div className="fw-bold text-primary mt-1">৳{(item.price * item.qty).toLocaleString()}</div>
        </div>
        <div className="d-flex align-items-center gap-2">
          <div className="input-group" style={{ width: '110px' }}>
            <button className="btn btn-outline-secondary btn-sm" onClick={() => onUpdateQty(item.id, item.qty - 1)}>-</button>
            <input type="number" className="form-control form-control-sm text-center" value={item.qty} readOnly />
            <button className="btn btn-outline-secondary btn-sm" onClick={() => onUpdateQty(item.id, item.qty + 1)}>+</button>
          </div>
          <button className="btn btn-outline-danger btn-sm" onClick={() => onRemove(item.id)}>
            <i className="fas fa-trash"></i>
          </button>
        </div>
      </div>
    </div>
  );
});

export default function Cart() {
  const { cart, removeFromCart, updateQty, total, clearCart } = useCart();
  const { isLoggedIn } = useAuth();
  const navigate = useNavigate();

  const handleCheckout = useCallback(() => {
    if (!isLoggedIn) {
      toast.info('Please login to proceed to checkout');
      navigate('/login');
      return;
    }
    navigate('/checkout');
  }, [isLoggedIn, navigate]);

  const handleRemove = useCallback((id) => {
    removeFromCart(id);
  }, [removeFromCart]);

  const handleUpdateQty = useCallback((id, qty) => {
    updateQty(id, qty);
  }, [updateQty]);

  if (cart.length === 0) {
    return (
      <div className="container py-5 text-center">
        <i className="fas fa-shopping-cart fa-4x text-muted mb-4"></i>
        <h4 className="text-muted">Your cart is empty</h4>
        <Link to="/products" className="btn btn-primary mt-3">Continue Shopping</Link>
      </div>
    );
  }

  return (
    <div className="container py-5">
      <h2 className="fw-bold mb-4" data-aos="fade-up">Shopping Cart</h2>
      <div className="row g-4">
        <div className="col-lg-8">
          {cart.map(item => (
            <CartItem 
              key={item.id} 
              item={item} 
              onRemove={handleRemove}
              onUpdateQty={handleUpdateQty}
            />
          ))}
          <button className="btn btn-outline-secondary btn-sm" onClick={clearCart}>
            <i className="fas fa-trash me-1"></i>Clear Cart
          </button>
        </div>

        <div className="col-lg-4">
          <div className="card border-0 shadow-sm" data-aos="fade-left">
            <div className="card-body">
              <h5 className="fw-bold mb-3">Order Summary</h5>
              <div className="d-flex justify-content-between mb-2">
                <span>Subtotal</span>
                <span>৳{total.toLocaleString()}</span>
              </div>
              <div className="d-flex justify-content-between mb-2">
                <span>Shipping</span>
                <span>৳60</span>
              </div>
              <hr />
              <div className="d-flex justify-content-between fw-bold fs-5 mb-3">
                <span>Total</span>
                <span className="text-primary">৳{(total + 60).toLocaleString()}</span>
              </div>
              <button className="btn btn-primary w-100" onClick={handleCheckout}>
                <i className="fas fa-lock me-2"></i>Proceed to Checkout
              </button>
              <Link to="/products" className="btn btn-outline-secondary w-100 mt-2">
                Continue Shopping
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
