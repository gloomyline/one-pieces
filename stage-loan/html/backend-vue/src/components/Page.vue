<template>
  <div class="block page">
    <el-pagination
      @current-change="handleCurrentChange"
      :current-page="safeCurrentPage"
      :page-size="pageSize"
      layout="total, prev, pager, next"
      :total="total">
    </el-pagination>
  </div>
</template>

<script>
export default {
  data() {
    return {};
  },
  props: {
    total: {
      type: Number,
      default: 1,
    },
    pageSize: {
      type: Number,
      default: 20,
    },
    currentPage: {
      type: Number,
      default: 1,
    },
  },
  computed: {
    safeCurrentPage() {
      // element-ui@1.1.2
      // el-pagination在currentPage超出total的时候，会把currentPage设成（貌似）最后一页。
      // 但是当页面第一次加载并且currentPage从url里面读出来时，total还没有从API返回，就会造成
      // 这种情况总是被自动跳到第一页／即最后一页。
      // 这里特殊处理还回避这个问题。
      if (this.total <= this.pageSize * (this.currentPage - 1)) {
        return 0;
      }
      return this.currentPage;
    },
  },
  watch: {
    currentPage: 'updateRoute',
    $route: 'routeChange',
  },
  methods: {
    routeChange() {
      // 手动或者第三方修改路由时的响应
      let page = Number(this.$route.query.page);
      if (isNaN(page)) {
        page = 1;
      }
      if (page !== this.currentPage) {
        this.handleCurrentChange(page);
      }
    },
    handleCurrentChange(page) {
      // el-pagination里面页面按钮被点击时的响应
      this.$emit('current-change', page);
    },
    updateRoute() {
      // prop currentPage变化时的响应：更新路由
      const path = this.$route.path;
      this.$router.push({
        path,
        query: { page: this.currentPage },
      });
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
