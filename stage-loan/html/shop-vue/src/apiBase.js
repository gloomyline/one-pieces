const devApiBase = 'http://fenqi-shop.wkdk.com:8081/';
export default process.env.NODE_ENV === 'development' ? devApiBase : '/';
export { devApiBase };
