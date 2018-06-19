<template>
    <div>
        <el-row>
            <el-col :span="24">
                <el-form :inline="true">
                    <el-form-item label="系统是否自动审核">
                        <el-switch
                                v-model="form.system_auto_review_switch"
                                active-color="#13ce66"
                                inactive-color="#ff4949"
                                active-value="on"
                                inactive-value="off">
                        </el-switch>
                    </el-form-item>
                    <el-form-item label="自动放款所需分值大于等于">
                        <el-input-number v-model="form.auto_loan_need_score" :min="0" :controls="false" size="small" style="width: 60px"></el-input-number> 分
                    </el-form-item>
                    <el-form-item>
                        <el-button type="primary" size="small" @click.native="handleSubmitModifySysAutoReview">保存修改</el-button>
                    </el-form-item>
                </el-form>
            </el-col>
        </el-row>
        <!--table-->
        <el-table :data="tableData" stripe border style="width: 100%;">
            <el-table-column property="id" label="ID" align="center" min-width="100"></el-table-column>
            <el-table-column property="name" label="信用等级" align="center" min-width="150" max-width="200"></el-table-column>
            <el-table-column property="start" label="起始分值(分)" align="center" min-width="150"></el-table-column>
            <el-table-column property="end" label="结束分值(分)" align="center" min-width="150"></el-table-column>
            <el-table-column label="操作" align="center" min-width="150">
                <template slot-scope="scope">
                    <el-button-group style="">
                        <el-button type="primary" size="small"  @click.native="handleUpdate(scope.row)">编辑</el-button>
                    </el-button-group>
                </template>
            </el-table-column>
        </el-table>
        <el-dialog title="编辑" :visible.sync="dialogFormVisible">
            <el-form :model="dialogForm" ref="dialogForm" label-width="120px" label-position="left">
                <el-form-item label="信用等级"  required>
                    <el-input v-model="dialogForm.name" placeholder="请输入信用等级名称" style="width: 180px"></el-input>
                </el-form-item>
                <el-form-item label="起始分值" required>
                    <el-input-number v-model="dialogForm.start" :min="0"></el-input-number>
                </el-form-item>
                <el-form-item label="结束分值" required>
                    <el-input-number v-model="dialogForm.end" :min="0"></el-input-number>
                </el-form-item>
            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click="dialogFormVisible = false">取 消</el-button>
                <el-button type="primary" @click="handleSubmitUpdate">提 交</el-button>
            </div>
        </el-dialog>
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
        system_auto_review_switch: 'off',
        auto_loan_need_score: 0,
      },
      dialogForm: {
        id: '',
        name: '',
        start: 0,
        end: 0,
      },
      dialogFormVisible: false,
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
      this.$http.get(`${apiBase}credit-set`)
        .then(getJsonAndCheckSuccess)
        .then((response) => {
          this.tableData = response.results.grade;
          this.form.system_auto_review_switch = response.results.system_auto_review_switch;
          this.form.auto_loan_need_score = response.results.auto_loan_need_score;
        }).catch(reportErrorMessage(this));
    },
    clearFilter() {
      this.form = {
        state: '',
        title: '',
      };
      this.getData();
    },
    handleUpdate(row) {
      this.dialogFormVisible = true;
      this.dialogForm.id = row.id;
      this.dialogForm.name = row.name;
      this.dialogForm.start = row.start;
      this.dialogForm.end = row.end;
    },
    handleSubmitUpdate() {
      if (Number(this.dialogForm.start) >= Number(this.dialogForm.end)) {
        this.$message.error('您输入的结束分值不能小于起始分值！请从新输入！');
        return;
      }
      const params = {
        id: this.dialogForm.id,
        name: this.dialogForm.name,
        start: this.dialogForm.start,
        end: this.dialogForm.end,
      };
      this.$http.put(`${apiBase}credit-grade-update`, params)
        .then(response => response.json())
        .then((json) => {
          if (json.status !== 'SUCCESS') {
            return Promise.reject(json.error_message);
          }
          this.$message({
            type: 'success',
            message: '编辑成功！',
          });
          this.getData();
          this.dialogFormVisible = false;
          return {};
        }).catch(reportErrorMessage(this));
    },
    handleSubmitModifySysAutoReview() {
      this.$confirm('您确定执行保存修改操作吗?', '提示', {
        type: 'warning',
      }).then(() => {
        const params = {
          system_auto_review_switch: this.form.system_auto_review_switch,
          auto_loan_need_score: this.form.auto_loan_need_score,
        };
        this.$http.put(`${apiBase}modify-sys-auto-review`, params)
          .then(response => response.json())
          .then((json) => {
            if (json.status !== 'SUCCESS') {
              return Promise.reject(json.error_message);
            }
            this.$message({
              type: 'success',
              message: '保存成功！',
            });
            this.getData();
            return {};
          }).catch(reportErrorMessage(this));
      }).catch(() => {});
    },
  },
};
</script>
