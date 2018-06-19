<template>
  <div class="block page">
    <el-pagination
      @current-change="handleCurrentChange"
      :current-page="safeCurrentPage"
      :page-size="pageSize()"
      layout="total, prev, pager, next, jumper"
      :total="total()">
    </el-pagination>
  </div>
</template>

<script>
import moment from 'moment';
import { mapGetters } from 'vuex';
import getters from '../store/getter-types';
import actions from '../store/action-types';
import commits from '../store/mutation-types';

export default {
  data() {
    return {
      leaveFiltersAlone: false,
    };
  },
  props: {
    filters: {
      type: Object,
      default: {},
    },
    /*
    {
      key（必填）: 传到页面的参数名，有的类型有多个就用空格隔开,
      type（必填）: 'input'(default) 'datePair' 'number' 'range',
      default: 默认值,
      preserved: 是否保留，有true值则清空筛选的时候不改变,
    }
    */
    filterMap: {
      type: Object,
      default: {},
    },
    setFilters: {
      type: Function,
    },
    filtersChanged: {
      type: Boolean,
      default: false,
    },
    clearFilters: {
      type: Boolean,
      default: false,
    },
  },
  computed: {
    offset() {
      return this.pageSize() * (this.currentPage() - 1);
    },
    safeCurrentPage() {
      // element-ui@1.1.2
      // el-pagination在currentPage超出total的时候，会把currentPage设成（貌似）最后一页。
      // 但是当页面第一次加载并且currentPage从url里面读出来时，total还没有从API返回，就会造成
      // 这种情况总是被自动跳到第一页／即最后一页。
      // 这里特殊处理还回避这个问题。
      if (this.total() <= this.offset) {
        return 0;
      }
      return this.currentPage();
    },
    filterParams() {
      return this.getParams(this.filters, this.filterMap);
    },
  },
  watch: {
    $route: 'routeChange',
    filtersChanged() {
      if (!this.filtersChanged) {
        return;
      }
      const prevPath = this.$route.fullPath;
      const page = this.$route.query.page || 1;
      this.updateRoute({ ...this.getParams(this.filters, this.filterMap), page }, true);
      if (prevPath === this.$route.fullPath) {
        this.fetchData();
      }
    },
    clearFilters() {
      if (!this.clearFilters) {
        return;
      }
      const prevPath = this.$route.fullPath;
      this.updateRoute({ ...this.getParams(this.filters, this.filterMap, true), page: 1 });
      if (prevPath === this.$route.fullPath) {
        this.fetchData();
      }
    },
  },
  mounted() {
    this.init();
  },
  methods: {
    ...mapGetters({
      total: getters.PAGER_GET_TOTAL,
      pageSize: getters.PAGER_GET_PAGE_SIZE,
      currentPage: getters.PAGER_GET_PAGE,
    }),
    getInitFilters() {
      const params = this.$route.query;
      return Object.keys(this.filterMap).reduce((total, param) => {
        const filter = this.filterMap[param];
        const filters = total;
        const keys = filter.key.split(' ');
        switch (filter.type) {
          case 'datePair':
            if (params[keys[0]] && params[keys[1]]) {
              filters[param] = [moment.unix(params[keys[0]]).format('YYYY-MM-DD'), moment.unix(params[keys[1]]).format('YYYY-MM-DD')];
            } else {
              filters[param] = filter.default || [];
            }
            break;
          case 'number':
            filters[param] = params[filter.key] === '' || params[filter.key] === undefined ? '' : Number(params[filter.key]);
            break;
          case 'range':
            if (params[keys[0]] !== '' && params[keys[0]] !== undefined && params[keys[1]] !== '' && params[keys[1]] !== undefined) {
              filters[param] = [Number(params[keys[0]]), Number(params[keys[1]])];
            } else {
              filters[param] = filter.default || [0, 0];
            }
            break;
          default:
            filters[param] = params[filter.key] === 0 ? 0 : params[filter.key] || filter.default || '';
            break;
        }
        return filters;
      }, {});
    },
    init() {
      const initFilters = this.getInitFilters();
      this.setFilters(initFilters);
      let page = this.safeCurrentPage;
      if (!isNaN(this.$route.query.page)) {
        page = Number(this.$route.query.page);
      }
      const prevPath = this.$route.fullPath;
      this.updateRoute({ ...this.getParams(initFilters, this.filterMap), page });
      if (prevPath === this.$route.fullPath) {
        this.fetchData();
      }
    },
    getParams(params, filterMap, onlyPreserved) {
      return Object.keys(filterMap).reduce((total, param) => {
        const filter = filterMap[param];
        if (onlyPreserved && !filter.preserved) {
          return total;
        }
        const filters = total;
        switch (filter.type) {
          case 'datePair':
            if (params[param] && params[param][0] && params[param][1]) {
              const keys = filter.key.split(' ');
              filters[keys[0]] = Date.parse(params[param][0]) / 1000;
              filters[keys[1]] = ((Date.parse(params[param][1]) / 1000) + (24 * 60 * 60)) - 1;
            } else {
              const keys = filter.key.split(' ');
              filters[keys[0]] = filters[keys[1]] = '';
            }
            break;
          case 'number':
            filters[filter.key] = (params[param] === '' || params[param] === undefined) ? '' : Number(params[param]);
            break;
          case 'range':
            if (params[param] && params[param][0] !== '' && params[param][0] !== undefined && params[param][1] !== '' && params[param][1] !== undefined) {
              const keys = filter.key.split(' ');
              filters[keys[0]] = params[param][0];
              filters[keys[1]] = params[param][1];
            } else {
              const keys = filter.key.split(' ');
              filters[keys[0]] = filters[keys[1]] = 0;
            }
            break;
          default:
            filters[filter.key] = params[param] === 0 ? 0 : (params[param] || filter.default || '');
            break;
        }
        return filters;
      }, {});
    },
    routeChange() {
      // 手动或者第三方修改路由时的响应
      if (this.leaveFiltersAlone) {
        this.leaveFiltersAlone = false;
      } else {
        this.setFilters(this.getInitFilters());
      }
      this.fetchData();
    },
    setTotal(total) {
      this.$store.commit(commits.PAGER_SET_TOTAL, total);
    },
    setPageSize(size, vue) {
      (vue || this).$store.commit(commits.PAGER_SET_PAGE_SIZE, size);
    },
    handleCurrentChange(page) {
      // el-pagination里面页面按钮被点击时的响应
      this.updateRoute({ ...this.filterParams, page });
    },
    updateRoute(params, leaveFiltersAlone) {
      // prop currentPage变化时的响应：更新路由
      this.leaveFiltersAlone = leaveFiltersAlone;
      const path = this.$route.path;
      this.$router.push({
        path,
        query: params,
      });
    },
    fetchData() {
      const params = this.$route.query;
      if (isNaN(this.$route.query.page)) {
        params.page = 1;
      }
      this.$store.dispatch(actions.PAGER_FETCH_DATA,
        { vue: this, fetchFunc: 'current-change', params });
    },
  },
};
</script>

<style scoped>
  .page {
    text-align: center;
    margin-top: 20px;
  }
</style>
