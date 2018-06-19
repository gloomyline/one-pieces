<template>
  <div class="nav-view">
    <nav class="tab-nav">
      <el-tabs v-model="currentPath" type="card" @tab-click="handleTabData()">
        <el-tab-pane v-for="tabItem in tabConfig" :label="tabItem.name" :name="tabItem.path" />
      </el-tabs>
    </nav>
    <router-view :tabData="tabData" class="nav-body" />
  </div>
</template>

<script>
  /**
   * nav-view
   * @description 导航视图组件，子组件中可通过props: { tabData: Object } 获取到tab的相应数据
   *
   * @param {array} config - 导航配置
   *
   * @example
   * const config = [
   *   {
   *     name: '全部',
   *     path: '/myorder/UserHome',
   *     data: 'hello', // 用于传递给对应子组件的数据
   *   },
   *   {
   *     name: '代发货',
   *     path: '/myorder/posts',
   *     data: 'hi',
   *   },
   * ];
   *
   * <nav-view :config="config" />
   */

  export default {
    data() {
      return {
        tabConfig: this.config,
        tabData: {},
        currentPath: undefined,
      };
    },
    created() {
      this.handleTabData(this.$route.path);
    },
    watch: {
      config: {
        handler(val) {
          this.tabConfig = val;
          this.handleTabData(this.$route.path);
        },
        deep: true,
      },
    },
    props: {
      config: Array,
    },
    methods: {
      handleTabData(path) {
        if (path) this.currentPath = path;
        const lowerPath = this.currentPath.toLowerCase();
        this.tabConfig.forEach((configItem, index) => {
          if (configItem.path.toLowerCase() === lowerPath) this.tabData = { ...configItem, index };
        });
        if (this.currentPath) {
          this.$router.replace(this.currentPath);
        }
      },
    },
  };
</script>

<style scoped>

  .nav-view {
    display: flex;
    flex: 1;
    flex-direction: column;
    overflow: hidden;
  }

  .nav-body {
    flex: 1;
    overflow: scroll;
  }
</style>
