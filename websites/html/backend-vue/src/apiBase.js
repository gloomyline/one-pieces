const devApiBase = 'http://local-admin.onepieces.com:8081/';
export default process.env.NODE_ENV === 'development' ? devApiBase : '/';
export { devApiBase };
