import { createContext, useContext, useState, useEffect, useMemo, useCallback } from 'react';
import { toast } from 'react-toastify';

const CartContext = createContext(null);

const CART_KEY = 'mv_cart';

const loadCart = () => {
  try { return JSON.parse(localStorage.getItem(CART_KEY)) || []; } catch { return []; }
};

export function CartProvider({ children }) {
  const [cart, setCart] = useState(loadCart);

  useEffect(() => {
    localStorage.setItem(CART_KEY, JSON.stringify(cart));
  }, [cart]);

  const addToCart = useCallback((product, qty = 1) => {
    setCart(prev => {
      const existing = prev.find(i => i.id === product.id);
      if (existing) {
        toast.info(`${product.name} quantity updated`);
        return prev.map(i => i.id === product.id ? { ...i, qty: i.qty + qty } : i);
      }
      toast.success(`${product.name} added to cart`);
      return [...prev, { ...product, qty }];
    });
  }, []);

  const removeFromCart = useCallback((id) => {
    setCart(prev => prev.filter(i => i.id !== id));
    toast.info('Item removed from cart');
  }, []);

  const updateQty = useCallback((id, qty) => {
    if (qty < 1) return removeFromCart(id);
    setCart(prev => prev.map(i => i.id === id ? { ...i, qty } : i));
  }, [removeFromCart]);

  const clearCart = useCallback(() => setCart([]), []);

  const total = useMemo(() => cart.reduce((sum, i) => sum + i.price * i.qty, 0), [cart]);
  const count = useMemo(() => cart.reduce((sum, i) => sum + i.qty, 0), [cart]);

  const value = useMemo(() => ({ 
    cart, 
    addToCart, 
    removeFromCart, 
    updateQty, 
    clearCart, 
    total, 
    count 
  }), [cart, addToCart, removeFromCart, updateQty, clearCart, total, count]);

  return (
    <CartContext.Provider value={value}>
      {children}
    </CartContext.Provider>
  );
}

export const useCart = () => useContext(CartContext);
