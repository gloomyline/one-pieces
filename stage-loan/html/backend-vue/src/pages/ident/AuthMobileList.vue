<template>
  <div>
    <el-row v-if="flag === false">
      <el-col :span="24">
        <el-form :inline="true">
          <el-form-item>
            <el-input v-model="form.user_name" placeholder="用户名"></el-input>
          </el-form-item>
          <el-form-item>
            <el-input v-model="form.real_name" placeholder="真实姓名"></el-input>
          </el-form-item>
          <el-form-item>
            <el-select v-model="form.state" placeholder="审核状态" clearable>
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
    <el-row v-if="flag === true">
      <el-button type="success" style="margin-bottom: 5px" @click.native="$router.back(-1)">返回上一页</el-button>
    </el-row>
    <el-table :data="tableData" stripe border style="width: 100%;">
      <el-table-column property="id" label="ID" align="center"></el-table-column>
      <el-table-column property="user_name" label="用户名" align="center"></el-table-column>
      <el-table-column property="real_name" label="真实姓名" align="center"></el-table-column>
      <el-table-column property="mobile" label="手机号码" align="center"></el-table-column>
      <el-table-column property="has_report" label="是否生成报告" align="center" :formatter="stateFormatter"></el-table-column>
      <!--<el-table-column property="is_phone_auth" label="状态" align="center" :formatter="isPhoneAuthFormatter"></el-table-column>-->
      <el-table-column property="state" label="审核状态" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.state | stateFilter }}</span>
        </template>
      </el-table-column>
      <el-table-column property="created_at" label="添加时间" align="center"></el-table-column>
      <el-table-column label="操作" align="center" >
        <template slot-scope="scope">
          <el-button-group>
            <el-button type="primary" size="small"  @click.native="handleReportShow(scope.row.id)" v-if="scope.row.has_report === 1">查看</el-button>
            <el-button type="danger" size="small"  @click.native="handleDelMobile(scope.row.id)">删除</el-button>
          </el-button-group>
        </template>
      </el-table-column>
    </el-table>
    <page :total="pageCount" :current-page="currentPage" :page-size="pageSize" @current-change="handleCurrentChange"></page>
  </div>
</template>
<script>
import apiBase from '../../apiBase';
import Page from '../../components/Page';
import { reportErrorMessage } from '../../ApiUtil';

export default {
  name: 'admins',
  data() {
    return {
      tableData: [],
      form: {
        user_name: '',
        state: '',
        real_name: '',
      },
      dialogData: {
        callAddress: '',
        callDateTime: '',
        callTimeLength: '',
        callType: '',
        mobileNo: '',
      },
      mobile: this.$route.query.mobile,
      pageCount: 0,
      currentPage: 1,
      pageSize: 20,
      flag: false,
      msg: '',
      states: [{ id: 'pass', name: '认证通过' }, { id: 'nopass', name: '认证不通过' }, { id: 'busy', name: '待认证' }],
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
      if (this.form.user_name !== '') {
        params.user_name = this.form.user_name;
      }
      if (this.form.real_name !== '') {
        params.real_name = this.form.real_name;
      }
      if (this.form.state !== '') {
        params.state = this.form.state;
      }
      if (this.mobile) {
        params.user_name = this.mobile;
        // this.form.state = this.state;
        this.flag = true;
      }
      this.$http.get(`${apiBase}auth-mobile`, { params })
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
        user_name: '',
        real_name: '',
        state: '',
      };
      this.getData();
    },
    stateFormatter(row) {
      return row.has_report === 1 ? '已生成' : '未生成';
    },
    isPhoneAuthFormatter(row) {
      return row.is_phone_auth === 1 ? '已通过' : '未通过';
    },
    handleAduitShow(id) {
      this.dialogFormVisible = true;
      this.dialogForm.user_id = id;
    },
    handleReportShow(id) {
      this.$router.push({
        path: `/mobile-detail/${id}`,
      });
    },
    handleDelMobile(id) {
      this.$confirm('确定删除该记录?', '提示', {
        type: 'warning',
      }).then(() => {
        this.$http.post(`${apiBase}del-mobile`, { mobile_id: id })
          .then(response => response.json())
          .then((json) => {
            if (json.status !== 'SUCCESS') {
              return Promise.reject(json.error_message);
            }
            this.$message({
              type: 'success',
              message: '删除成功！',
            });
            this.getData();
            return {};
          }).catch(reportErrorMessage(this));
      }).catch(() => {});
    },
  },
  filters: {
    stateFilter(v) {
      switch (v) {
        case 'pass' :
          return '认证通过';
        case 'nopass' :
          return '认证不通过';
        case 'busy' :
          return '待认证';
        default:
          return '';
      }
    },
  },
};
</script>
<style>
  .mobilereporttitle{
    text-align: center;
    font-weight:bold;
    font-size:15px;
  }
  .mobilereportdialog {
    text-align: center;  
  }
</style>