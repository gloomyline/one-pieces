import gets from '../getter-types';
import acts from '../action-types';
import commits from '../mutation-types';

const state = {
  total: 0,
  pageSize: 20,
  currentPage: 1,
};

const getters = {
  [gets.PAGER_GET_TOTAL]: st => st.total,
  [gets.PAGER_GET_PAGE_SIZE]: st => st.pageSize,
  [gets.PAGER_GET_PAGE]: st => st.currentPage,
};

const actions = {
  [acts.PAGER_FETCH_DATA](context, { vue, fetchFunc, params }) {
    const filters = {};
    Object.keys(params).forEach((param) => {
      if (param === 'page') {
        context.commit(commits.PAGER_SET_PAGE, params[param]);
      } else {
        filters[param] = params[param];
      }
    });
    vue.$emit(fetchFunc, {
      ...filters,
      offset: context.state.pageSize * (context.state.currentPage - 1),
      limit: context.state.pageSize,
    }, vue);
  },
};

const mutations = {
  [commits.PAGER_SET_TOTAL](st, total) {
    st.total = Number(total || 0); // eslint-disable-line
  },
  [commits.PAGER_SET_PAGE_SIZE](st, size) {
    st.pageSize = Number(size || 0); // eslint-disable-line
  },
  [commits.PAGER_SET_PAGE](st, page) {
    st.currentPage = Number(page || 1); // eslint-disable-line
  },
};

export default {
  state,
  getters,
  actions,
  mutations,
};
