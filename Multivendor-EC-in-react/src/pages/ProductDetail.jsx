import { useEffect, useState, useMemo, useCallback } from 'react';
import { useParams, Link } from 'react-router-dom';
import AOS from 'aos';
import { useCart } from '../context/CartContext';
import { imgUrl } from '../config';
import api from '../api/api';

export default function ProductDetail() {
  const { id } = useParams();
  const [product, setProduct] = useState(null);
  const [images, setImages] = useState([]);
  const [mainImg, setMainImg] = useState('');
  const [qty, setQty] = useState(1);
  const [loading, setLoading] = useState(true);
  const { addToCart } = useCart();

  useEffect(() => {
    AOS.refresh();
    setLoading(true);
    api.get(`/products.php?id=${id}`)
      .then(r => {
        const p = r.data.data?.product;
        if (p) {
          setProduct(p);
          const imgs = p.images || [];
          setImages(imgs);
          setMainImg(imgs[0]?.image_path || p.primary_image || '');
        }
      })
      .catch(() => {})
      .finally(() => setLoading(false));
  }, [id]);

  const handleAddToCart = useCallback(() => {
    if (product) {
      addToCart({
        id: product.id,
        name: product.name,
        price: parseFloat(product.price),
        image: mainImg || product.primary_image,
        vendor: product.vendor_name,
      }, qty);
    }
  }, [product, mainImg, qty, addToCart]);

  const discount = useMemo(() => {
    if (!product) return 0;
    return product.compare_price > product.price
      ? Math.round(((product.compare_price - product.price) / product.compare_price) * 100)
      : 0;
  }, [product]);

  const incrementQty = useCallback(() => {
    setQty(q => Math.min(product.stock_quantity, q + 1));
  }, [product]);

  const decrementQty = useCallback(() => {
    setQty(q => Math.max(1, q - 1));
  }, []);

  if (loading) return <div className="text-center py-5"><div className="spinner-border text-primary"></div></div>;
  if (!product) return <div className="container py-5 text-center text-muted">Product not found.</div>;

  return (
    <div className="container py-5">
      <nav aria-label="breadcrumb" className="mb-4" data-aos="fade-up">
        <ol className="breadcrumb">
          <li className="breadcrumb-item"><Link to="/">Home</Link></li>
          <li className="breadcrumb-item"><Link to="/products">Products</Link></li>
          <li className="breadcrumb-item active">{product.name}</li>
        </ol>
      </nav>

      <div className="row g-4">
        {/* Images */}
        <div className="col-lg-5" data-aos="fade-right">
          <div className="border rounded overflow-hidden mb-3" style={{ height: '380px' }}>
            <img
              src={imgUrl(mainImg || product.primary_image)}
              className="w-100 h-100"
              style={{ objectFit: 'contain' }}
              alt={product.name}
              onError={e => { e.target.src = 'https://placehold.co/500x400?text=No+Image'; }}
            />
          </div>
          {images.length > 1 && (
            <div className="d-flex gap-2 flex-wrap">
              {images.map((img, i) => (
                <img
                  key={i}
                  src={imgUrl(img.image_path)}
                  onClick={() => setMainImg(img.image_path)}
                  className={`rounded border cursor-pointer ${mainImg === img.image_path ? 'border-primary border-2' : ''}`}
                  style={{ width: 70, height: 70, objectFit: 'cover', cursor: 'pointer' }}
                  alt=""
                  onError={e => { e.target.src = 'https://placehold.co/70x70?text=img'; }}
                />
              ))}
            </div>
          )}
        </div>

        {/* Info */}
        <div className="col-lg-7" data-aos="fade-left">
          <h2 className="fw-bold mb-2">{product.name}</h2>
          <p className="text-muted mb-2">
            <i className="fas fa-store me-1"></i>
            <Link to={`/shops/${product.vendor_id}`} className="text-decoration-none">
              {product.vendor_name}
            </Link>
          </p>

          {/* Rating */}
          <div className="d-flex align-items-center mb-3">
            {[1,2,3,4,5].map(s => (
              <i key={s} className={`fas fa-star ${s <= Math.round(product.rating || 0) ? 'text-warning' : 'text-muted'}`}></i>
            ))}
            <small className="text-muted ms-2">({product.review_count || 0} reviews)</small>
          </div>

          {/* Price */}
          <div className="mb-3">
            <span className="fs-3 fw-bold text-primary">৳{parseFloat(product.price).toLocaleString()}</span>
            {product.compare_price > product.price && (
              <>
                <span className="text-muted text-decoration-line-through ms-3 fs-5">
                  ৳{parseFloat(product.compare_price).toLocaleString()}
                </span>
                <span className="badge bg-danger ms-2">{discount}% OFF</span>
              </>
            )}
          </div>

          {product.short_description && (
            <p className="text-muted mb-3">{product.short_description}</p>
          )}

          <div className="mb-3">
            <span className={`badge ${product.stock_quantity > 0 ? 'bg-success' : 'bg-danger'}`}>
              {product.stock_quantity > 0 ? `${product.stock_quantity} in stock` : 'Out of stock'}
            </span>
            {product.category_name && (
              <span className="badge bg-secondary ms-2">{product.category_name}</span>
            )}
          </div>

          {/* Qty + Cart */}
          <div className="d-flex align-items-center gap-3 mb-4">
            <div className="input-group" style={{ width: '130px' }}>
              <button className="btn btn-outline-secondary" onClick={decrementQty}>-</button>
              <input type="number" className="form-control text-center" value={qty} readOnly />
              <button className="btn btn-outline-secondary" onClick={incrementQty}>+</button>
            </div>
            <button
              className="btn btn-primary btn-lg flex-grow-1"
              onClick={handleAddToCart}
              disabled={product.stock_quantity < 1}
            >
              <i className="fas fa-cart-plus me-2"></i>Add to Cart
            </button>
          </div>

          {product.description && (
            <div className="border-top pt-3">
              <h6 className="fw-semibold">Description</h6>
              <p className="text-muted">{product.description}</p>
            </div>
          )}
        </div>
      </div>
    </div>
  );
}
