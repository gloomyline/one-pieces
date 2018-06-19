const devApiBase = 'http://local-admin.wkdk.com:8081/';
export default process.env.NODE_ENV === 'development' ? devApiBase : '/';
export { devApiBase };
