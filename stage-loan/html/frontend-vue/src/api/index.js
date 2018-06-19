/*
* @Author: AlanWang
* @Date:   2017-09-07 09:33:58
 * @Last modified by:
 * @Last modified time: 2018-02-02T09:42:00+08:00
*/

/**
 * API 接口
 */

import * as req from './base'
import session from '@/common/js/sessionStorage'

class ApiController {
  constructor (opts) {
    this.opts = opts
    this.reqAuthorization = {headers: {'Authorization': `${session.loadLoginStatus().token}`}}
  }

  /**
   * 获取注册短信验证码
   * @param  {[Object]} postData {mobile: ''}
   * @param  {[Function]} cb callback
   * @return {[type]}   response
   */
  async fetchVerification (postData, cb) {
    postData = postData || {}
    let res = await req.reqAByPost('1/send-mobile-code/new-user', postData)
    cb && cb(res)
    return res
  }

  /**
   * 注册
   * @param  {[Object]} postData {mobile: '', code: '', password: ''}
   * @return {[type]}               返回请求响应
   */
  async signUp (postData) {
    postData = postData || {}
    let res = await req.reqMByPost('signup', postData)
    let token = res.results[0].token
    session.saveLoginStatus(token)
    this.reqAuthorization = {headers: {'Authorization': token}} // 注册成功后更新token
    return res
  }

  /**
   * 获取忘记密码短信验证码
   * @param  {Object}   postData {mobile: ''}
   * @param  {Function} cb       callback
   * @return {[type]}            response
   */
  async fetchVerifictionForForgetPwd (postData, cb) {
    let res = await req.reqAByPost('1/send-mobile-code/forget-password', postData)
    cb && cb(res)
    return res
  }

  /**
   * 重置用户登录密码
   * @param  {[Object]}   putData {mobile: '手机号', code: '验证码', password: '新密码'}
   * @param  {Function} cb      callback
   * @return {[type]}           response
   */
  async resetPwd (putData, cb) {
    let res = await req.reqMByPut('forget-password', putData)
    cb && cb(res)
  }

  /**
   * 登录
   * @param  {[Object]} postData 请求数据 { mobile: '', password: '' }
   * @return {[Object]}            返回请求响应
   */
  async login (postData) {
    postData = postData || {}
    let res = await req.reqMByPost('login', postData)
    let token = res.results[0].token
    session.saveLoginStatus(token)
    this.reqAuthorization = {headers: {'Authorization': token}} // 登录成功后更新token
    return res
  }

  /**
   * 退出登陆
   * @return {[Object]} 请求响应
   */
  async logout () {
    let res = await req.reqMByGet('logout')
    if (res.status === 'SUCCESS') {
      session.clearLoginStatus()
    }
    return res
  }

  async fetchMobile () {
    let res = await req.reqMByGet('get-mobile', this.reqAuthorization)
    return res
  }

  /**
   * 获取首页数据
   * @return {[Object]}          返回results<Array>第一个
   */
  async fetchHomeData () {
    let res = await req.reqMByGet('home', this.reqAuthorization)
    return res.results[0]
  }

  /**
   * 申请借款
   * @param  {[Object]} postData [description]
   * @return {[Object]}          返回results<Array>
   */
  async applyLoan (postData) {
    postData = postData || {}
    let res = await req.reqMByPost('loan-confirm', postData, this.reqAuthorization)
    return res.results
  }

  /**
   * 借款订单确认
   * @param  {Object} postData {amount: 500, period: 7}
   * @return {Object}          返回response
   */
  async confirmLoan (postData) {
    postData = postData || {}
    let res = await req.reqMByPost('loan-submit', postData, this.reqAuthorization)
    return res
  }

  /**
   * 还款请求服务端构造连连支付(第三方)请求参数
   * @param  {[type]} postData [description]
   * @return {[type]}          [description]
   */
  async refundLoan (postData) {
    let res = await req.reqMByPost('llpay-auth-pay', postData, this.reqAuthorization)
    return res.results[0]
  }

  /**
   * fenqi home api
   */
  /**
   * fetch base auth state
   * @return {Promise} [description]
   */
  async getBaseAuthState () {
    let res = await req.reqMByGet('get-base-auth-state', this.reqAuthorization)
    return res.results
  }
  /**
   * fetch available quota for user to lend cash
   * @return {Promise} [description]
   */
  async getAvailableQuota () {
    let res = await req.reqMByGet('get-quota', this.reqAuthorization)
    return res.results
  }

  /**
   * fetch max quota for user
   * @return {Promise} [description]
   */
  async getMaxQuota () {
    let res = await req.reqMByGet('get-product-quota', this.reqAuthorization)
    return res.results
  }

  /**
   * fetch loan detail for cash consume
   * @return {Promise} [description]
   */
  async cashTimesLoan () {
    let res = await req.reqMByGet('cash-installment', this.reqAuthorization)
    return res
  }

  /**
   * confirm cash lend
   * @param  {[type]}  postData {amount: '借款金额【integer】', use: '用途【string】', period: '借款分期期限【integer】'}
   * @return {Promise}          [description]
   */
  async cashLend (postData) {
    let res = await req.reqMByPost('loan-cash-submit', postData, this.reqAuthorization)
    return res
  }

  /**
   * [searchShop description]
   * @param  {[type]}  payload { shop_no: '商户号【必填】' }
   * @return {Promise}         [description]
   */
  async searchShop (payload) {
    let opts = Object.assign({}, this.reqAuthorization, { params: payload })
    let res = await req.reqMByGet('shop-base', opts)
    return res
  }

  /**
   * [fetchShopProductCate description]
   * @param  {[type]}  payload { shop_no: '商户号【必填】' }
   * @return {Promise}         [description]
   */
  async fetchShopProductCate (payload) {
    let opts = Object.assign({}, this.reqAuthorization, { params: payload })
    let res = await req.reqMByGet('shop-category', opts)
    return res
  }

  /**
   * [fetchShopProducts description]
   * @param  {[type]}  payload {
   *                            shop_id: '商户ID【必填】, 通过shop-base接口获得',
   *                            category_id: '分类ID【必填】,通过shop-category接口获得',
   *                            offset: '查询的基准数，默认【0】',
   *                            limit: '查询的记录数，默认【10】'
   *                           }
   * @return {Promise}         [description]
   */
  async fetchShopProducts (payload) {
    let opts = Object.assign({}, this.reqAuthorization, { params: payload })
    let res = await req.reqMByGet('shop-product', opts)
    return res
  }

  /**
   * [fetchShopProduct description]
   * @param  {[type]}  payload { shop_product_id: '商户产品ID【必填】, 通过shop-product接口获得' }
   * @return {Promise}         [description]
   */
  async fetchShopProduct (payload) {
    let opts = Object.assign({}, this.reqAuthorization, { params: payload })
    let res = await req.reqMByGet('shop-product-detail', opts)
    return res
  }

  /**
   * [fetchAmount description]
   * @param  {[type]}  payload { term: '期数', amount: '分期总金额' }
   * @return {Promise}         [description]
   */
  async fetchAmount (payload) {
    let res = await req.reqMByPost('get-amount', payload, this.reqAuthorization)
    return res
  }

  /**
   * [submitConsume description]
   * @param  {Object}  payload {
   *                              period: '借款期数【必填】',
   *                              shop_id: '商户ID【必填】',
   *                              details: '产品详情【必填】【array】'
   *                            }
   * @return {Promise}         [description]
   */
  async submitConsume (payload) {
    let res = await req.reqMByPost('loan-consume-submit', payload, this.reqAuthorization)
    return res
  }

  /**
   * mall api relation
   * @type {[api restfull]}
   */
  /**
   * fetch shop cates
   * @type {[type]}
   */
  async fetchShopCates () {
    let res = await req.reqMByGet('get-category', this.reqAuthorization)
    return res
  }
  /**
   * fetch shops list
   * @param  {[type]}  payload {
   *   category_id: '分类id 为空时，默认all （通过get-category接口获得）',
   *   city_id: '城市id 为空时，默认all （通过get-static-config接口获得【type=shopAreas】）'
   * }
   * @return {Promise}         [description]
   */
  async fetchShops (payload) {
    let opts = Object.assign({}, this.reqAuthorization, { params: payload })
    let res = await req.reqMByGet('shops', opts)
    return res
  }

  /**
   * fetch shop detail
   * @param  {[type]}  payload { shop_id: '商户id' }
   * @return {Promise}         [description]
   */
  async fetchShopDetail (payload) {
    let opts = Object.assign({}, this.reqAuthorization, { params: payload })
    let res = await req.reqMByGet('shop-detail', opts)
    return res
  }

  /**
   * me realtion
   * @type {[type]}
   */
  /**
   * fetch repaying records
   * @param  {[type]}  payload [{ offset: @type(Number), limit: @type(Number) }]
   * @return {Promise}
   */
  async fetchLends (payload) {
    let opts = Object.assign({}, this.reqAuthorization, { params: payload })
    let res = await req.reqMByGet('repaying-record', opts)
    return res
  }

  /**
   * fetch repaying record detail
   * @param  {[type]}  payload { loan_id: '借款ID' }
   * @return {Promise}         [description]
   */
  async fetchLend (payload) {
    let opts = Object.assign({}, this.reqAuthorization, { params: payload })
    let res = await req.reqMByGet('repaying-detail', opts)
    return res
  }

  /**
   * confirm to settle
   * @param  {[type]}  payload { loan_id: '借款ID' }
   * @return {Promise}         [description]
   */
  async confirmSettle (payload) {
    let res = await req.reqMByPost('confirm-settle', payload, this.reqAuthorization)
    return res
  }

  /**
   * llpay-auth-pay-settle
   * @param  {[type]}  payload { loan_id: '借款ID' }
   * @return {Promise}         [description]
   */
  async settle (payload) {
    let res = await req.reqMByPost('llpay-auth-pay-settle', payload, this.reqAuthorization)
    return res
  }

  /**
   * llpay-auth-pay
   * @param  {[type]}  payload { loan_id: '借款ID' }
   * @return {Promise}         [description]
   */
  async imediate (payload) {
    let res = await req.reqMByPost('llpay-auth-pay', payload, this.reqAuthorization)
    return res
  }

  /**
   * lend & refund - records
   * @type {[type]}
   */
  /**
   * fetch lend records
   * @param  {[type]}  payload { offset: '查询基准，默认值【0】', limit: '查询记录数，默认值【10】' }
   * @return {Promise}         [description]
   */
  async fetchLendsList (payload) {
    let opts = Object.assign({}, this.reqAuthorization, { params: payload })
    let res = await req.reqMByGet('loan-record', opts)
    return res
  }

  async fetchLendsDetail (payload) {
    let opts = Object.assign({}, this.reqAuthorization, { params: payload })
    let res = await req.reqMByGet('loan-record-detail', opts)
    return res
  }

  async fetchRefundsList (payload) {
    let opts = Object.assign({}, this.reqAuthorization, { params: payload })
    let res = await req.reqMByGet('repayed-record', opts)
    return res
  }

  /**
   * shop settle
   * @param payload {
   *                  shop_name: '商家名称 【必填】',
   *                  contacts_name: '联系人姓名 【必填】',
   *                  contacts_mobile: '联系方式 【必填】',
   *                  contacts_addr: '联系地址 【必填】'
   *                }
   */
  async submitShop (payload) {
    let res = await req.reqMByPost('shop-settled', payload, this.reqAuthorization)
    return res
  }

  // /////////
  // 认证   //
  // /////////
  /**
   * 获取用户认证信息状态
   * @return {[type]}          返回response
   */
  async fetchUserAuthenStatus (cb) {
    let res = await req.reqMByGet('user-auth-state', this.reqAuthorization)
    cb(res)
  }

  /**
   * 获取用户认证后的信息
   * @return {[type]}
   */
  async fetchAutenticatedIdentity (cb) {
    let res = await req.reqMByGet('lm-get-identity', this.reqAuthorization)
    cb && cb(res.results[0])
  }

  /**
   * 身份证认证
   * @param  {[Object]}   postData {username: '', identityno: ''}
   * @param   {[Function]} cb     响应成功回调
   * @return {[type]}            [description]
   */
  async authenticateIdentity (postData, cb) {
    postData = postData || {}
    let res = await req.reqMByPost('lm-identity', postData, this.reqAuthorization)
    cb && cb(res)
  }

  /**
   * 获取已认证的银行列表
   * @param  {Function} cb     callback
   * @return {Object}          response
   */
  async fetchBankcards (cb) {
    let res = await req.reqMByGet('auth-bankcard-get', this.reqAuthorization)
    cb && cb(res)
    return res
  }

  /**
   * 设置默认银行卡
   * @param {Object}   postData {id: '用户银行卡ID'}
   * @param {Function} cb       callback
   * @return {Object} response
   */
  async setDefaultBankcard (postData, cb) {
    let res = await req.reqMByPut('set-bankcard-default', postData, this.reqAuthorization)
    cb && cb(res)
    return res
  }

  /**
   * 校验银行卡是否合法
   * @param  {Object}   params {card_no: '银行卡号'}
   * @param  {Function} cb     callback
   * @return {[type]}          response
   */
  async validateBankcard (params, cb) {
    let options = Object.assign({}, this.reqAuthorization, { params })
    let res = await req.reqMByGet('llpay-cardbin', options)
    cb && cb(res)
    return res
  }

  /**
   * 添加银行卡(认证)
   * @param {Object}   data { id_no: '证件号码', card_no: '银行卡号', acct_name: '姓名' }
   * @param {Function} cb   callback
   */
  async addBankcard (params, cb) {
    let options = Object.assign({}, this.reqAuthorization, { params })
    let res = await req.reqMByGet('llpay-sign', options)
    cb && cb(res)
    return res
  }

  /**
   * 请求连连支付第三方接口
   * @param  {Object}   postData {req_data: JSON String}
   * @param  {Function} cb       callback
   * @return {[type]}            html(better to request by formHTMLElement.submit())
   */
  async authenticateBankcard (postData, cb) {
    let res = await req.reqTByPost('https://wap.lianlianpay.com/signApply.htm', postData)
    cb && cb(res)
    return res
  }

  /**
   * 手机认证
   * @param  {[Object]} postData {servicecode: '手机服务密码'}
   * @return {[type]}
   */
  async authenticatePhone (postData) {
    let res = await req.reqMByPost('lm-mobile', postData, this.reqAuthorization)
    if (res.status === 'SUCCESS') {
      this.phoneAthenticationToken = res.results[0].token
    }
    return res
  }

  /**
   * 手机认证所需运营商验证码
   * @param  {[type]} postData {token: '立木token', smscode: '运营商发送的验证码'}
   * @return {[type]}
   */
  async authenticatePhoneVerification (postData) {
    let _postData = Object.assign({}, postData, {token: this.phoneAthenticationToken})
    let res = await req.reqMByPost('lm-mobile-input', _postData, this.reqAuthorization)
    return res
  }

  /**
   * 手机认证轮询
   * @return {[type]} [description]
   */
  async authenticatePhoneContinue () {
    let res = await req.reqMByPost('lm-mobile-continue', {token: this.phoneAthenticationToken}, this.reqAuthorization)
    return res
  }

  /**
   * 央行征信
   * @type {Object} payload: {username: '用户账户', password: '密码', middle_auth_code: '央行征信身份验证码'}
   * @return Promise
   */
  async authForCredit (payload) {
    let res = await req.reqMByPost('lm-credit-auth', payload, this.reqAuthorization)
    return res
  }

  /**
   * 公积金
   * @param  {[Object]}  payload { username: '用户账号', password: '密码', area: '地区编号'}
   * @return {Promise}         [description]
   */
  async authForHousefund (payload) {
    let res = await req.reqMByPost('lm-get-house-fund', payload, this.reqAuthorization)
    return res
  }

  /**
   * [authForSocialSecurity description]
   * @param  {object}  payload {username: '用户账号', password: '密码', area: '地区编号'}
   * @return {Promise}         [description]
   */
  async authForSocialSecurity (payload) {
    let res = await req.reqMByPost('lm-get-social-security', payload, this.reqAuthorization)
    return res
  }

  /**
   * 通用认证(QQ/微信/银行卡号)
   * @param  {Object} postData {auth_type: '认证类型，仅支持【qq、wechat、bankcard】取值', account: '账号'}
   * @return {Object}          {status: 'SUCCESS', error_message: '认证成功'}
   */
  async authenticateForUpLimit (postData) {
    let res = await req.reqMByPost('auth-common', postData, this.reqAuthorization)
    return res
  }

  /**
   * 通用认证查询(QQ/微信/银行卡号)
   * @param  {Object} params {auth_type: '认证类型，仅支持【qq、wechat、bankcard】取值'}
   * @return {Object}        response
   */
  async fetchAuthenticateForUpLimit (params) {
    let options = Object.assign({}, { params }, this.reqAuthorization)
    let res = await req.reqMByGet('auth-common-get', options)
    return res
  }

  /**
   * 学历认证
   * @param  {Object} postData { username: '用户账号', password: '用户密码' }
   * @return {[type]}          [description]
   */
  async authenticateDiploma (postData) {
    let res = await req.reqMByPost('lm-education', postData, this.reqAuthorization)
    return res
  }

  /**
   * [submitPhoto description]
   * @param  {[Object]}  payload {file: '待上传的图片文件'}
   * @return {Promise}         [description]
   */
  async submitPhoto (payload, cb) {
    let res = await req.reqMByPost('sign-pic-auth', payload, this.reqAuthorization)
    cb && cb(res)
    return res
  }

  /**
   * 京东认证
   * @param  {[type]} postData { username: '用户京东帐号', password: '用户京东密码' }
   * @return {[type]}          [description]
   */
  async authenticateJingdong (postData) {
    let res = await req.reqMByPost('lm-jd', postData, this.reqAuthorization)
    if (res.status === 'SUCCESS') {
      this.jingdongAthenticationToken = res.results[0].token
    }
    return res
  }

  /**
   * 京东认证输入验证码
   * @param  {[type]} postData { smscode: '短信印证码' }
   * @return {[type]}          [description]
   */
  async authenticateJingdongVerification (postData) {
    let _postData = Object.assign({}, postData, {token: this.jingdongAthenticationToken})
    let res = await req.reqMByPost('lm-jd-input', _postData, this.reqAuthorization)
    return res
  }

  /**
   * 京东认证轮询状态
   * @return {[type]} [description]
   */
  async authenticateJingdongContinue () {
    let res = await req.reqMByPost('lm-jd-continue', {token: this.jingdongAthenticationToken}, this.reqAuthorization)
    return res
  }

  /**
   * 淘宝认证
   * @return {[type]} [description]
   */
  async authentiacteTaobao () {
    let res = await req.reqMByPost('lm-taobao', {}, this.reqAuthorization)
    if (res.status === 'SUCCESS') {
      this.taobaoAuthenticationToken = res.results[0].token
    }
    return res
  }

  /**
   * 淘宝认证轮询
   * @return {[type]} [description]
   */
  async authentiacteTaobaoContinue () {
    let res = await req.reqMByPost('lm-taobao-continue', {token: this.taobaoAuthenticationToken}, this.reqAuthorization)
    return res
  }

  /**
   * 信用卡账单认证
   * @param  {[type]} postData { username: '邮箱账号', password: '邮箱密码（QQ邮箱时可不填）' }
   * @return {[type]}          [description]
   */
  async authenticateCreditBill (postData) {
    let res = await req.reqMByPost('lm-bill', postData, this.reqAuthorization)
    if (res.status === 'SUCCESS') {
      this.creditBillAuthenticationToken = res.results[0].token
    }
    return res
  }

  /**
   * 信用卡账单轮询
   * @return {[type]} [description]
   */
  async authenticateCreditBillContinue () {
    let res = await req.reqMByPost('lm-bill-continue', { token: this.creditBillAuthenticationToken }, this.reqAuthorization)
    return res
  }

  /**
   * 网银流水认证
   * @param  {[type]} postData {username: '网银账号', password: '网银密码', bank: '银行编号'}
   * @return {[type]}          [description]
   */
  async authenticateEbank (postData) {
    let res = await req.reqMByPost('lm-ebank', postData, this.reqAuthorization)
    return res
  }

  // ///////////
  // 个人信息 //
  // ///////////
  /**
   * 获取用户个人基本信息
   * @return {[type]} [description]
   */
  async fetchPersonalInfo (cb) {
    let res = await req.reqMByGet('user-profile', this.reqAuthorization)
    cb && cb(res.results[0])
  }

  /**
   * 保存用户个人基本信息
   * @param  {[type]} postData {live_area: '(必填)居住区域', live_addr: '(必填)详细地址', live_time: '(必填)居住时长''}
   * @return {[type]}          [description]
   */
  async savePersonalInfo (postData, cb) {
    let res = await req.reqMByPost('user-save-profile', postData, this.reqAuthorization)
    cb && cb(res)
  }

  /**
   * 获取工作信息
   * @return {[type]} [description]
   */
  async fetchWorkInfo (cb) {
    let res = await req.reqMByGet('user-work', this.reqAuthorization)
    cb && cb(res.results[0])
  }

  /**
   * 保存工作信息
   * @param  {[type]} postData {industry: '', position: '', company_name: '', company_area: '', company_addr: '', company_tel: ''}
   * @return {[type]}          [description]
   */
  async saveWorkInfo (postData, cb) {
    let res = await req.reqMByPost('user-save-work', postData, this.reqAuthorization)
    cb && cb(res)
  }

  /**
   * 获取用户关系信息
   * @param  {Function} cb [description]
   * @return {[type]}      [description]
   */
  async fetchRelationInfo (cb) {
    let res = await req.reqMByGet('user-relation', this.reqAuthorization)
    cb && cb(res.results[0])
  }

  /**
   * 保存用户关系信息
   * @param  {[Object]}   postData {linkman_relation_fir: '', linkman_name_fir: '', linkman_tel_fir: '', linkman_relation_sec: '', linkman_name_sec: '', linkman_tel_sec: ''}
   * @param  {Function} cb       [description]
   * @return {[type]}            [description]
   */
  async saveRelationInfo (postData, cb) {
    let res = await req.reqMByPost('user-save-relation', postData, this.reqAuthorization)
    cb && cb(res)
  }

  /**
   * 提升额度
   * @param  {Function} cb [description]
   * @return {[type]}      [description]
   */
  async upperLimit (cb) {
    let res = await req.reqMByGet('quota-apply', this.reqAuthorization)
    cb && cb(res)
    return res
  }

  // ///////////
  // 个人中心 //
  // ///////////
  /**
   * 获取用户借款记录列表
   * @param  {Function} cb callback
   * @return {[Array]}      借款记录列表
   */
  async fetchLoanRecords (data, cb) {
    let options = Object.assign({}, this.reqAuthorization, {params: data})
    let res = await req.reqMByGet('loan-record', options)
    cb && cb(res)
  }

  /**
   * 根据 loan_id 获取单条借款记录详情
   * @param  {[Object]}   data {loan_id: ''}
   * @param  {Function} cb   [description]
   * @return {[Object]}        单条借款记录
   */
  async fetchLoanRecordById (data, cb) {
    let options = Object.assign({}, this.reqAuthorization, {params: data})
    let res = await req.reqMByGet('loan-record-detail', options)
    cb && cb(res.results[0])
  }

  /**
   * 获取用户还款记录列表
   * @param  {Function} cb callback
   * @return {[type]}      还款记录列表
   */
  async fetchRepaymentRecords (data, cb) {
    let options = Object.assign({}, this.reqAuthorization, {params: data})
    let res = await req.reqMByGet('repayments-record', options)
    cb && cb(res)
  }

  /**
   * 获取用户代金券列表
   * @param  {Object}   data {offset: '开始位置', limit: '返回代金券数目'}
   * @param  {Function} cb   callback
   * @return {Object}        response {results: [](代金券数组), has_more: ''(是否还有未加载完全的代金券)}
   */
  async fetchVouchers (data, cb) {
    let options = Object.assign({}, this.reqAuthorization, {params: data})
    let res = await req.reqMByGet('cash-coupon-get', options)
    return res
  }

  /**
   * 问题反馈
   * @param  {Object}   postData {type: Object={credit: '信用不足', info: '资料填写', loan: '借款', repayment: '还款', func: '功能建议', other: '其他'}}
   * @param  {Function} cb       callback
   * @return {Promisition}
   */
  async feedback (postData, cb) {
    let res = await req.reqMByPost('feedback', postData, this.reqAuthorization)
    cb && cb(res)
    return res
  }

  /**
   * 修改登录密码
   * @param  {Object}   postData {old_password: '', new_password: '', repeat_password: ''}
   * @param  {Function} cb       callback
   * @return {[type]}            Promisifition
   */
  async modifyLoginPwd (postData, cb) {
    let res = await req.reqMByPut('password', postData, this.reqAuthorization)
    cb && cb(res)
    return res
  }
}

export default new ApiController()
