const devApiBase = 'http://fenqi-admin.wkdk.com:8081/';
export default process.env.NODE_ENV === 'development' ? devApiBase : '/';
export { devApiBase };
