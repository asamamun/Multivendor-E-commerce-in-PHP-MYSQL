export const API_BASE = 'http://localhost/round68/reactJS/projects/backend/apis';
export const ASSETS_BASE = 'http://localhost/round68/reactJS/projects/Multivendor-E-commerce-in-PHP-MYSQL/';

export const imgUrl = (path) => {
  if (!path) return 'https://placehold.co/400x300?text=No+Image';
  if (path.startsWith('http')) return path;
  return ASSETS_BASE + path;
};
