/**
 * @Date:   2017-12-18T17:18:38+08:00
 * @Last modified time: 2018-01-04T13:56:08+08:00
 */
const express = require('express')
const router = express.Router()
const Mock = require('mockjs')
// extend Random
const Random = Mock.Random
Random.extend({
  telephone: function () {
    const prefixArr = ['13', '14', '15', '17', '18']
    let prefix = prefixArr[parseInt(5 * Math.random())]
    let randomNum = ''
    for (let i = 0; i < 9; ++i) {
      randomNum += Math.floor(Math.random() * 10)
    }
    return prefix + randomNum
  },
  banners: [
    'http://pic.wkdk.cn/75a5b23d1eebc4f783bead1d12884c957f54edc5.jpg',
    'http://pic.wkdk.cn/d8893a590b89d30cda17fc65b2e18062c69fa681.jpg',
    'http://pic.wkdk.cn/1c8b3ea4a5c8d11bc3098fe010101ff52837e8ff.jpg'
  ],
  banner: function () {
    return this.pick(this.banners)
  }
})

// const shopData = require('../mocks/datas/shopData.json')

router.get('/shops', (req, res) => {
  const results = Mock.mock({
    'shops|10-20': [{
      'id|+1': 0,
      'shop_name': '@WORD(5, 8)',
      'shop_cate|0-6': 1,
      'shop_addr': '@WORD(12, 20)',
      'shop_tel': '@TELEPHONE',
      'logo': "@IMAGE('125x125', '#02adea', '#000', 'png', '@shop_name')",
      'desc': '@PARAGRAPH(3, 6)',
      'banners|3-5': ['@BANNER']
    }]
  })

  res.json({ status: 'SUCCESS', results, error_message: null })
})

/**
 * shop data
 * @params payload { shopId: '' }
 * @return null
 */
router.get('/shop/:shopId', (req, res) => {
  // let shopId = this.req.params.shopId
  const results = Mock.mock({
    'banner|3-6': ['@BANNER'],
    'shop_name': '@WORD(5, 10)',
    'shop_logo': "@IMAGE('125x125', '#02adea', '#000', 'png', '@shop_name')",
    'shop_title': '@WORD(10, 16)',
    'project_cates|1-6': ['@INTEGER(0, 6)'],
    'projects|2-8': [{
      'project_name': '@WORD(6, 12)',
      'project_img': '@BANNER',
      'project_fee|300-10000': 1,
      'project_first_fee|100-200': 1,
      'invetory|10-100': 1,
      'intro': '@PARAGRAPH(3, 6)',
      'standard': '@PARAGRAPH(1, 3)',
      'service': '@PARAGRAPH(2, 4)',
      'cate': '@INTEGER(0, 6)'
    }]
  })

  res.json({ status: 'SUCCESS', results, error_message: null })
})

module.exports = router
