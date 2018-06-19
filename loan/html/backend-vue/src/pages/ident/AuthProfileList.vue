<template>
  <div>
    <el-row>
      <el-col :span="24">
        <el-form :inline="true">
          <el-form-item>
            <el-input v-model="form.user_name" placeholder="用户名"></el-input>
          </el-form-item>
          <el-form-item>
            <el-input v-model="form.real_name" placeholder="真实姓名"></el-input>
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
    <el-table :data="tableData" stripe border style="width: 100%;">
      <el-table-column property="id" label="ID" align="center"></el-table-column>
      <el-table-column property="user_name" label="用户名" align="center"></el-table-column>
      <el-table-column property="real_name" label="真实姓名" align="center"></el-table-column>
      <el-table-column property="live_area" label="现居城市" align="center"></el-table-column>
      <el-table-column property="live_addr" label="详细地址" align="center"></el-table-column>
      <el-table-column property="live_time" label="居住时长" align="center"></el-table-column>
      <el-table-column property="created_at" label="添加时间" align="center"></el-table-column>
      <el-table-column label="工作信息" align="center">
        <template slot-scope="scope">
          <el-button-group style="">
            <el-button type="primary" size="small" align="center" @click.native="handleWork(scope.row.id)">查看</el-button>
          </el-button-group>
        </template>
      </el-table-column>
      <el-table-column label="人际关系" align="center">
        <template slot-scope="scope">
          <el-button-group style="">
            <el-button type="primary" size="small" align="center" @click.native="handleRelation(scope.row.id)">查看</el-button>
          </el-button-group>
        </template>
      </el-table-column>
    </el-table>
    <page :total="pageCount" :current-page="currentPage" :page-size="pageSize" @current-change="handleCurrentChange"></page>

    <div>
      <el-dialog :visible.sync="dialogFormVisible">
        <p style="margin-left:50px; line-height: 25px;" v-html="msg"></p>
        <span slot="footer" class="dialog-footer">
          <el-button @click.native="dialogFormVisible = false">关闭</el-button>
        </span>
      </el-dialog>
    </div>
    <div>
      <el-dialog :visible.sync="dialogWorkFormVisible">
        <el-col :span="24" class="bg-blue content-header" style="font-size: 24px"><div class="grid-content " data-height="50" align="center">工作信息</div></el-col>
        <el-col :span="24" class="profilemargin">
          <el-col :span="6" class="authprofiledialog">从事职业</el-col>
          <el-col :span="18" v-model="dialogData.industry">{{ dialogData.industry }}</el-col>
        </el-col>
        <el-col :span="24" class="profilemargin">
          <el-col :span="6" class="authprofiledialog">从事岗位</el-col>
          <el-col :span="18" v-model="dialogData.position">{{ dialogData.position }}</el-col>
        </el-col>
        <el-col :span="24" class="profilemargin">
          <el-col :span="6" class="authprofiledialog">单位名称</el-col>
          <el-col :span="18" v-model="dialogData.company_name">{{ dialogData.company_name }}</el-col>
        </el-col>
        <el-col :span="24" class="profilemargin">
          <el-col :span="6" class="authprofiledialog">单位所在地</el-col>
          <el-col :span="18" v-model="dialogData.company_area">{{ dialogData.company_area }}</el-col>
        </el-col>
        <el-col :span="24" class="profilemargin">
          <el-col :span="6"class="authprofiledialog">详细地址</el-col>
          <el-col :span="18" v-model="dialogData.company_addr">{{ dialogData.company_addr }}</el-col>
        </el-col>
        <el-col :span="24" class="profilemargin">
          <el-col :span="6" class="authprofiledialog">单位电话</el-col>
          <el-col :span="18" v-model="dialogData.company_tel">{{ dialogData.company_tel }}</el-col>
        </el-col>
        <span slot="footer" class="dialog-footer">
          <el-button type="primary" @click.native="dialogWorkFormVisible = false">关闭</el-button>
        </span>
      </el-dialog>
    </div>
    <div>
      <el-dialog :visible.sync="dialogRelationFormVisible">
        <el-col :span="24" class="bg-blue content-header" style="font-size: 24px"><div class="grid-content " data-height="50" align="center">人际关系</div></el-col>
        <el-col :span="24" style="margin-top: 10px;">
          <el-col :span="6" class="authprofiledialog">联系人1</el-col>
        </el-col>
        <el-col :span="24" class="profilemargin">
          <el-col :span="6" class="authprofiledialog">与本人关系</el-col>
          <el-col :span="18" v-model="relationData.linkman_relation_fir">{{ relationData.linkman_relation_fir | relationFilter }}</el-col>
        </el-col>
        <el-col :span="24" class="profilemargin">
          <el-col :span="6" class="authprofiledialog">姓名</el-col>
          <el-col :span="18" v-model="relationData.linkman_name_fir">{{ relationData.linkman_name_fir }}</el-col>
        </el-col>
        <el-col :span="24" class="profilemargin">
          <el-col :span="6" class="authprofiledialog">电话</el-col>
          <el-col :span="18" v-model="relationData.linkman_tel_fir">{{ relationData.linkman_tel_fir }}</el-col>
        </el-col>
        <el-col :span="24" style="border-bottom: solid 1px;"></el-col>
        <el-col :span="24" class="profilemargin">
          <el-col :span="6" class="authprofiledialog">联系人2</el-col>
        </el-col>
        <el-col :span="24" class="profilemargin">
          <el-col :span="6" class="authprofiledialog">与本人关系</el-col>
          <el-col :span="18" v-model="relationData.linkman_relation_sec">{{ relationData.linkman_relation_sec | relationFilter }}</el-col>
        </el-col>
        <el-col :span="24" class="profilemargin">
          <el-col :span="6" class="authprofiledialog">姓名</el-col>
          <el-col :span="18" v-model="relationData.linkman_name_sec">{{ relationData.linkman_name_sec }}</el-col>
        </el-col>
        <el-col :span="24" class="profilemargin">
          <el-col :span="6" class="authprofiledialog">电话</el-col>
          <el-col :span="18" v-model="relationData.linkman_tel_sec">{{ relationData.linkman_tel_sec }}</el-col>
        </el-col>
        <span slot="footer" class="dialog-footer">
          <el-button type="primary" @click.native="dialogRelationFormVisible = false">关闭</el-button>
        </span>
      </el-dialog>
    </div>
    <el-dialog
            title="提示"
            :visible.sync="dialogVisible"
            :before-close="handleClose">
      <span>{{ msg }}</span>
      <span slot="footer" class="dialog-footer">
        <el-button type="primary" @click="dialogVisible = false">关 闭</el-button>
     </span>
    </el-dialog>
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
        user_name: '',
        state: '',
        real_name: '',
      },
      dialogData: '',
      relationData: '',
      pageCount: 0,
      currentPage: 1,
      pageSize: 20,
      msg: '',
      dialogVisible: false,
      dialogWorkFormVisible: false,
      dialogRelationFormVisible: false,
      states: [{ id: 0, name: '未填写' }, { id: 1, name: '已填写' }],
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
      this.$http.get(`${apiBase}auth-profile`, { params })
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
    handleWork(id) {
      this.$http.get(`${apiBase}work-msg/${id}`)
      .then(response => response.json())
      .then((json) => {
        if (json.status === 'SUCCESS') {
          if (json.results.length === 0) {
            this.dialogVisible = true;
            this.msg = '暂无工作信息';
          } else {
            this.dialogWorkFormVisible = true;
            this.dialogData = json.results;
          }
        }
      }).catch(() => {
        this.$message('系统错误');
      });
    },
    handleRelation(id) {
      this.$http.get(`${apiBase}relation-msg/${id}`)
      .then(response => response.json())
      .then((json) => {
        if (json.status === 'SUCCESS') {
          if (json.results.length === 0) {
            this.dialogVisible = true;
            this.msg = '暂无人际关系记录';
          } else {
            this.dialogRelationFormVisible = true;
            this.relationData = json.results;
          }
        }
      }).catch(() => {
        this.$message('系统错误');
      });
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
      return row.state === 1 ? '已填写' : '未填写';
    },
  },
  filters: {
    relationFilter(v) { // 关系
      switch (v) {
        case '0' : return '同事';
        case '1' : return '兄弟';
        case '2' : return '父母';
        case '3' : return '姐妹';
        case '4' : return '朋友';
        default : return '';
      }
    },
  },
};
</script>
<style scoped>
  .authprofiledialog{
    color: #0f0f0f;font-weight: bolder;
  }
  .profilemargin{
    margin-bottom: 10px;
    margin-top: 10px;
  }
</style>