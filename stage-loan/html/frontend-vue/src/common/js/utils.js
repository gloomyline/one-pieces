/*
* @Author: AlanWang
* @Date:   2017-08-30 10:43:34
 * @Last modified by:
 * @Last modified time: 2017-12-21T15:58:54+08:00
*/

function _clearObject (obj) {
  for (const k of Object.keys(obj)) {
    const v = obj[k]
    if (typeof v === 'object' && v !== null) {
      _clearObject(v)
    } else {
      obj[k] = null
    }
  }
  return obj
}

export function clearObjectRecursively (obj) {
  return _clearObject(obj)
}

export function isPhoneNumber (val) {
  let _val = Number(val)
  return /^1(3|4|5|7|8)\d{9}$/.test(_val)
}

export function isChineseName (val) {
  // if (!checkType(val, 'str', `perferrence val is needed String, but got ${val.constructor}`)) return
  let reg = /^([\u4e00-\u9fcb]|[\uE7C7-\uE7F3]){2,4}$/i
  return reg.test(val)
}

/**
 * 验证身份证号码字符串是否为合法的
 * @param  {[String]}  val 待校验字符串
 * @return {Boolean}     校验结果
 */
export function isIDNumber (val) {
  let city = {11: '北京', 12: '天津', 13: '河北', 14: '山西', 15: '内蒙古', 21: '辽宁', 22: '吉林', 23: '黑龙江 ', 31: '上海', 32: '江苏', 33: '浙江', 34: '安徽', 35: '福建', 36: '江西', 37: '山东', 41: '河南', 42: '湖北 ', 43: '湖南', 44: '广东', 45: '广西', 46: '海南', 50: '重庆', 51: '四川', 52: '贵州', 53: '云南', 54: '西藏 ', 61: '陕西', 62: '甘肃', 63: '青海', 64: '宁夏', 65: '新疆', 71: '台湾', 81: '香港', 82: '澳门', 91: '国外'}
  let birthday = val.substr(6, 4) + '/' + Number(val.substr(10, 2)) + '/' + Number(val.substr(12, 2))
  let d = new Date(birthday)
  let newBirthday = d.getFullYear() + '/' + Number(d.getMonth() + 1) + '/' + Number(d.getDate())
  let currentTime = new Date().getTime()
  let time = d.getTime()
  let arrInt = [7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2]
  let arrCh = ['1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2']
  let sum = 0
  let i
  let residue

  if (!/^\d{17}(\d|x)$/i.test(val)) return false
  if (city[val.substr(0, 2)] === undefined) return false
  if (time >= currentTime || birthday !== newBirthday) return false
  for (i = 0; i < 17; ++i) {
    sum += val.substr(i, 1) * arrInt[i]
  }
  residue = arrCh[sum % 11]
  if (residue !== val.substr(17, 1)) return false

  return true
}

/**
 * 验证银行卡字符串是否合法
 * @param  {String}  val 待校验银行卡号字符串
 * @return {Boolean}     校验结果
 */
export function isBankcardNumber (val) {
  return true
}

export function isPassword (val) {
  let len = val.length
  if (len < 6 || len > 18) {
    return {isValid: false, errMsg: '密码不能少于6位且不多于18位'}
  }
  // else if (!(/\W+/.test(val))) {
  //   return {isValid: false, errMsg: '密码首字符必须是字母'}
  // }
  return {isValid: true, errMsg: ''}
}

/**
 * 将时间戳转换成 ?天:?时:?分:?秒 类型时间字符串
 * @param  {[type]} timeStamp [description] 时间戳单位秒
 * @return {[type]}           [description] 时间字符串
 */
export function formatDateString (timeStamp) {
  timeStamp = Math.floor(timeStamp / 1000)
  let days = Math.floor(timeStamp / (24 * 3600))
  let hours = Math.floor(timeStamp % (24 * 3600) / 3600)
  let minutes = Math.floor(timeStamp % (60 * 60) / 60)
  let seconds = timeStamp % 60
  return `${days}天：${hours}时：${minutes >= 10 ? minutes : '0' + minutes}分：${seconds >= 10 ? seconds : '0' + seconds}秒`
}

/**
 * @param {String} val just a string value
 * @return {String} a string which is not included white characters
 */
export function rmWhite (val) {
  return String(val).replace(/\s+/g, '')
}
