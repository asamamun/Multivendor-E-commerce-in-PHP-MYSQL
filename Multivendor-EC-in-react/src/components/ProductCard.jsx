import { Link } from 'react-router-dom';
import { useCart } from '../context/CartContext';
import { imgUrl } from '../config';
import { memo, useCallback } from 'react';

const ProductCard = ({ product }) => {
  const { addToCart } = useCart();

  const handleAdd = useCallback((e) => {
    e.preventDefault();
    addToCart({
      id: product.id,
      name: product.name,
      price: parseFloat(product.price),
      image: product.primary_image,
      vendor: product.vendor_name,
    });
  }, [product, addToCart]);

  const discount = product.compare_price > product.price
    ? Math.round(((product.compare_price - product.price) / product.compare_price) * 100)
    : 0;

  return (
    <div className="card h-100 shadow-sm border-0 product-card" data-aos="fade-up">
      <Link to={`/products/${product.id}`} className="text-decoration-none">
        <div className="overflow-hidden" style={{ height: '200px' }}>
          <img
            src={imgUrl(product.primary_image)}
            className="card-img-top product-img"
            alt={product.name}
            style={{ height: '200px', objectFit: 'cover', transition: 'transform 0.4s' }}
            onError={e => { e.target.src = 'https://placehold.co/400x300?text=No+Image'; }}
          />
        </div>
      </Link>
      <div className="card-body d-flex flex-column">
        {discount > 0 && (
          <span className="badge bg-danger mb-1" style={{ width: 'fit-content' }}>{discount}% OFF</span>
        )}
        <Link to={`/products/${product.id}`} className="text-decoration-none text-dark">
          <h6 className="card-title fw-semibold mb-1 text-truncate">{product.name}</h6>
        </Link>
        <small className="text-muted mb-2">
          <i className="fas fa-store me-1"></i>{product.vendor_name}
        </small>
        <div className="mt-auto">
          <div className="d-flex align-items-center justify-content-between">
            <div>
              <span className="fw-bold text-primary fs-6">৳{parseFloat(product.price).toLocaleString()}</span>
              {product.compare_price > product.price && (
                <small className="text-muted text-decoration-line-through ms-2">
                  ৳{parseFloat(product.compare_price).toLocaleString()}
                </small>
              )}
            </div>
          </div>
          <button
            className="btn btn-primary btn-sm w-100 mt-2"
            onClick={handleAdd}
            disabled={product.stock_quantity < 1}
          >
            <i className="fas fa-cart-plus me-1"></i>
            {product.stock_quantity < 1 ? 'Out of Stock' : 'Add to Cart'}
          </button>
        </div>
      </div>
    </div>
  );
};

export default memo(ProductCard);
