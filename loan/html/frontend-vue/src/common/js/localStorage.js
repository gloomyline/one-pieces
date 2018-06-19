/*
* @Author: AlanWang
* @Date:   2017-11-14 16:14:32
* @Last Modified by:   AlanWang
* @Last Modified time: 2017-11-14 17:03:26
*/
'use strict'

const ls = window.localStorage

export function setSesameAuth (user) {
  let sesameAuth = JSON.parse(ls.getItem('sesameAuth'))
  if (sesameAuth && Object.keys(sesameAuth).length !== 0) {
    sesameAuth[user] = true
    ls.setItem('sesameAuth', JSON.stringify(sesameAuth))
  } else {
    let _sesameAuth = {}
    _sesameAuth[user] = true
    ls.setItem('sesameAuth', JSON.stringify(_sesameAuth))
  }
}

export function getSesameAuth (user) {
  let sesameAuth = JSON.parse(ls.getItem('sesameAuth'))
  return sesameAuth ? sesameAuth[user] : false
}

