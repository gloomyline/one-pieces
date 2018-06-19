<template>
    <div>
        <el-row>
            <el-col :span="24">
                <el-form :inline="true">
                    <el-form-item>
                        <el-input v-model="form.title" placeholder="表名"></el-input>
                    </el-form-item>
                    <el-form-item>
                        <el-select v-model="form.state" placeholder="状态" clearable>
                            <el-option
                                    v-for="item in states"
                                    :label="item.name"
                                    :value="item.id">
                            </el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item>
                        <el-button type="primary" @click.native="filter">查找</el-button>
                        <el-button type="primary" @click.native="clearFilter">清空筛选</el-button>
                    </el-form-item>
                </el-form>
            </el-col>
        </el-row>
        <!--table-->
        <el-table :data="tableData" stripe border style="width: 100%;" name="risk" class="risk-table">
            <el-table-column property="title" label="表名" align="center" min-width="200" max-width="250"></el-table-column>
            <el-table-column property="description" label="表注释" align="center" min-width="150" max-width="200"></el-table-column>
            <el-table-column property="items" label="表字段详情" align="center" min-width="250">
                <template slot-scope="scope">
                     <span v-for="(v,k) in scope.row.items">
                    {{ v }}、
                </span>
                </template>
            </el-table-column>
            <el-table-column property="enable" label="状态" align="center" width="100">
                <template slot-scope="scope">
                    <span>{{ scope.row.enable | filterEnable }}</span>
                </template>
            </el-table-column>
        </el-table>
    </div> 
</template>
<script>
import apiBase from '../../apiBase';
import { getJsonAndCheckSuccess, reportErrorMessage } from '../../ApiUtil';

export default {
  name: 'Risk',
  data() {
    return {
      tableData: [],
      form: {
        title: '',
        state: '',
      },
      states: [{ id: 0, name: '关闭' }, { id: 1, name: '开启' }],
    };
  },
  mounted() {
    this.getData();
  },
  methods: {
    filter() {
      this.getData();
    },
    getData() {
      const params = {};
      if (this.form.title !== '') {
        params.title = this.form.title;
      }
      if (this.form.state !== '') {
        params.state = this.form.state;
      }
      this.$http.get(`${apiBase}risk`, { params })
        .then(getJsonAndCheckSuccess)
        .then((response) => {
          this.tableData = response.results;
        }).catch(reportErrorMessage(this));
    },
    clearFilter() {
      this.form = {
        state: '',
        title: '',
      };
      this.getData();
    },
  },
  filters: {
    filterEnable(v) {
      switch (v) {
        case 1 :
          return '开启';
        case 0 :
          return '关闭';
        default:
          return '';
      }
    },
  },
};
</script>
