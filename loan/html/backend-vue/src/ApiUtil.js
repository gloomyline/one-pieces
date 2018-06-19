function getJsonAndCheckSuccess(response) {
  return response.json().then((json) => {
    if (json.status !== 'SUCCESS') {
      return Promise.reject(json.error_message);
    }
    return json;
  });
}

function reportErrorMessage(vue) {
  return (e) => {
    vue.$message({
      showClose: true,
      message: e,
      type: 'error',
      duration: 0,
    });
  };
}

export {
  getJsonAndCheckSuccess,
  reportErrorMessage,
};
