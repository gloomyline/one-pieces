/**
 * @comment:route of shop in consume module
 * @Date:   2018-01-03T17:15:19+08:00
 * @Last modified time: 2018-01-31T14:11:05+08:00
 */
import Shop from '@/pages/Container/Children/Home/Children/Consume/Shop'
import Product from '@/pages/Container/Children/Home/Children/Consume/Product'
// import PersonalPhoto from '@/pages/Container/Children/Home/Children/Consume/PersonalPhoto'
// import ProductIntro from '@/pages/Container/Children/Home/Children/Consume/ProductIntro'
// import ProductSpec from '@/pages/Container/Children/Home/Children/Consume/ProductSpec'
// import ProductService from '@/pages/Container/Children/Home/Children/Consume/ProductService'
import SubmitResult from '@/pages/Container/Children/Home/Children/Consume/SubmitResult'

export default {
  path: 'shop',
  name: 'consumeShop',
  component: Shop,
  meta: {
    requiresAuth: true,
    requiresHideBar: true
  },
  children: [
    {
      path: 'product/:productId',
      name: 'productDetail',
      component: Product,
      meta: {
        requiresAuth: true,
        requiresHideBar: true
      }
      // children: [
      //   {
      //     path: 'intro',
      //     name: 'productIntro',
      //     component: ProductIntro
      //   },
      //   {
      //     path: 'spec',
      //     name: 'productSpec',
      //     component: ProductSpec
      //   },
      //   {
      //     path: 'service',
      //     name: 'productService',
      //     component: ProductService
      //   }
      // ]
    },
    // { // 亲签照
    //   path: 'personal-photo',
    //   name: 'personalCenter',
    //   component: PersonalPhoto,
    //   meta: {
    //     requiresAuth: true,
    //     requiresHideBar: true
    //   }
    // },
    {
      path: 'submit-result',
      name: 'submitResult',
      component: SubmitResult,
      meta: {
        requiresAuth: true,
        requiresHideBar: true
      }
    }
  ]
}
