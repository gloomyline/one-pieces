<template>
  <div>
    <el-row>
      <el-col :span="24">
        <el-form :inline="true">
          <el-form-item>
            <el-input v-model="form.mobile" placeholder="用户名"></el-input>
          </el-form-item>
          <el-form-item>
            <el-input v-model="form.real_name" placeholder="真实姓名"></el-input>
          </el-form-item>
          <el-form-item>
            <el-select v-model="form.type" placeholder="额度类型" clearable>
              <el-option
                      v-for="item in types"
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
    <el-table :data="tableData" stripe border style="width: 100%;">
      <el-table-column property="id" label="ID" align="center" min-width="80"></el-table-column>
      <el-table-column property="mobile" label="用户名" align="center" min-width="150"></el-table-column>
      <el-table-column property="real_name" label="真实姓名" align="center" min-width="120"></el-table-column>
      <el-table-column property="apply_quota" label="申请额度" align="center" min-width="100" :formatter="quotaFormatter"></el-table-column>
      <el-table-column property="allow_quota" label="通过额度" align="center" min-width="100"></el-table-column>
      <el-table-column property="apply_type" label="额度类型" align="center" min-width="120">
        <template slot-scope="scope">
          <span>{{ scope.row.apply_type | filterType }}</span>
        </template>
      </el-table-column>
      <el-table-column property="memo" label="备注" align="center" min-width="180"></el-table-column>
      <el-table-column property="state" label="审核结果" align="center" min-width="100">
        <template slot-scope="scope">
          <span>{{ scope.row.state | filterState }}</span>
        </template>
      </el-table-column>
      <el-table-column property="admin_name" label="审核人员" align="center" min-width="100"></el-table-column>
      <el-table-column property="updated_at" label="审核时间" align="center" width="180">
      </el-table-column>
    </el-table>
    <page :total="pageCount" :current-page="currentPage" :page-size="pageSize" @current-change="handleCurrentChange"></page>

  </div>
</template>
<script>
import apiBase from '../../apiBase';
import Page from '../../components/Page';

export default {
  name: 'admins',
  data() {
    return {
      tableData: [],
      form: {
        mobile: '',
        type: '',
        real_name: '',
      },
      pageCount: 0,
      currentPage: 1,
      pageSize: 20,
      types: [{ id: 0, name: '后台添加' }, { id: 1, name: '用户申请' }, { id: 2, name: '系统添加' }],
    };
  },
  components: {
    Page,
  },
  mounted() {
    if (!isNaN(this.$route.query.page)) {
      this.currentPage = Number(this.$route.query.page);
    }
    this.getData();
  },
  methods: {
    getData() {
      const params = {
        limit: this.pageSize,
        offset: this.pageSize * (this.currentPage - 1),
      };
      if (this.form.mobile !== '') {
        params.mobile = this.form.mobile;
      }
      if (this.form.real_name !== '') {
        params.real_name = this.form.real_name;
      }
      if (this.form.type !== '') {
        params.type = this.form.type;
      }
      this.$http.get(`${apiBase}quota-log`, { params })
      .then(response => response.json())
      .then((json) => {
        if (json.status === 'SUCCESS') {
          this.tableData = json.results;
          this.pageCount = json.count;
        }
      }).catch(() => {
        this.$message('系统错误');
      });
    },
    handleCurrentChange(page) {
      this.currentPage = page;
      this.getData();
    },
    filter() {
      this.currentPage = 1;
      this.getData();
    },
    clearFilter() {
      this.form = {
        mobile: '',
        real_name: '',
        type: '',
      };
      this.getData();
    },
    quotaFormatter(row) {
      return row.apply_type === 1 ? '----' : row.apply_quota;
    },
  },
  filters: {
    filterState(v) {
      switch (v) {
        case 0 :
          return '待审核';
        case 1 :
          return '通过';
        case 2 :
          return '失败';
        default:
          return '';
      }
    },
    filterType(v) {
      switch (v) {
        case 0 :
          return '后台添加';
        case 1 :
          return '用户申请';
        case 2 :
          return '系统添加';
        default:
          return '';
      }
    },
  },
};
</script>