<template>
    <div>
        <el-row>
            <el-col :span="24">
                <el-form :inline="true">
                    <el-form-item>
                        <el-input v-model="form.name" placeholder="规则名称"></el-input>
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
                        <el-button type="success" @click.native="handleAdd">添加</el-button>
                    </el-form-item>
                </el-form>
            </el-col>
        </el-row>
        <!--统计总分信息-->
        <el-row>
            <el-col :span="24" style="font-weight: bolder">当前规则总分为<span style="color: #67C23A">{{ totalScore }}</span>分，其中有效分为<span style="color: #67C23A">{{ totalValidScore }}</span>分</el-col>
        </el-row>
        <!--table-->
        <el-table :data="tableData" stripe border style="width: 100%;" name="risk" class="risk-table">
            <el-table-column property="name" label="字段" align="center" min-width="180" max-width="200"></el-table-column>
            <el-table-column property="module" label="表注释" align="center" min-width="130" max-width="180" :formatter="moduleFormatter"></el-table-column>
            <el-table-column property="pattern" label="模式" align="center" min-width="120" max-width="150" :formatter="patternFormatter"></el-table-column>
            <el-table-column property="auth_state" label="认证状态" align="center" min-width="120" max-width="180" :formatter="authStateFormatter"></el-table-column>
            <el-table-column property="outcome" label="结果" align="center" min-width="80" max-width="120" :formatter="outcomeFormatter"></el-table-column>
            <el-table-column property="score" label="分值" align="center" min-width="80" max-width="120" :formatter="scoreFormatter"></el-table-column>
            <el-table-column property="state" label="状态" align="center" min-width="80" max-width="120" :formatter="stateFormatter"></el-table-column>
            <el-table-column property="remarks" label="备注" align="center" width="200"></el-table-column>
            <el-table-column label="操作" align="center" min-width="180">
                <template slot-scope="scope">
                    <el-button-group style="">
                        <el-button type="primary" size="small"  @click.native="handleUpdate(scope.row)">编辑</el-button>
                        <el-button type="danger" size="small" @click.native="handleUpdateState(scope.row.id)" v-if="scope.row.state === 2">禁用</el-button>
                        <el-button type="primary" size="small" @click.native="handleUpdateState(scope.row.id)" v-if="scope.row.state === 1">启用</el-button>
                        <el-button type="warning" size="small"  @click.native="handleDel(scope.row.id)">删除</el-button>
                    </el-button-group>
                </template>
        </el-table-column>
        </el-table>
        <page :total="pageCount" :current-page="currentPage" :page-size="pageSize" @current-change="handleCurrentChange"></page>

        <div>
            <el-dialog :visible.sync="dialogFormVisible":title='dialogForm.title' @close="handleClose">
                <el-form :model="dialogForm" ref="dialogForm" label-width="120px" label-position="left">
                    <el-form-item label="表注释" required>
                        <el-select v-model="dialogForm.module" placeholder="表注释"  @change="changeModule(dialogForm.module)" clearable >
                            <el-option v-for="(v,k) in riskModule" :label="v.description" :value="k"></el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="模式" required>
                        <el-select v-model="dialogForm.pattern" placeholder="请选择" @change="changePattern(dialogForm.pattern)">
                            <el-option v-for="(item,index) in patterns" :label="item.name" :value="item.id"></el-option>
                        </el-select>
                    </el-form-item>
                    <el-row><h3>规则配置</h3></el-row>
                    <el-form-item label="表字段"  required>
                        <el-select v-model="dialogForm.name" placeholder="请选择">
                            <el-option v-for="(item,index) in riskItems" :label="item" :value="index + ',' + item"></el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="认证状态" required>
                        <el-select v-model="dialogForm.operator" placeholder="请选择">
                            <el-option v-for="(item,index) in operators" :label="item.name" :value="item.id"></el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="值" v-if="seen">
                        <el-input v-model="dialogForm.val" label="值" style="width: 220px"></el-input><span style="color: #2e8ded">若取值为区间则用逗号隔开例如:1,100</span><span>{{ errTxt }}</span>
                    </el-form-item>
                    <el-form-item label="结果" required>
                        <el-select v-model="dialogForm.outcome" placeholder="请选择">
                            <el-option v-for="(item,index) in outcomes" :label="item.name" :value="item.id"></el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="分数" required>
                        <el-select v-model="dialogForm.symbol" placeholder="请选择" style="width: 80px">
                            <el-option v-for="(item,index) in symbols" :label="item.name" :value="item.id"></el-option>
                        </el-select>
                        <el-input-number v-model="dialogForm.score"  :min="0" :max="1000" label="分数" ></el-input-number>
                    </el-form-item>
                    <el-form-item label="状态"  required>
                        <el-radio-group v-model="dialogForm.state">
                            <el-radio :label="2">启用</el-radio>
                            <el-radio :label="1">禁用</el-radio>
                        </el-radio-group>
                    </el-form-item>
                    <el-form-item label="备注">
                        <el-input
                                type="textarea"
                                :rows="3"
                                :maxlength='200'
                                style="width: 80%"
                                placeholder="请输入内容"
                                v-model="dialogForm.remarks">
                        </el-input>

                    </el-form-item>
                </el-form>
                <span slot="footer" class="dialog-footer">
                  <el-button type="primary" @click.native="handleSubmit" v-if="dialogForm.title === '添加规则'">保存</el-button>
                  <el-button type="primary" @click.native="handleSubmitUpdate" v-if="dialogForm.title === '编辑规则'">保存修改</el-button>
                  <el-button @click.native="dialogFormVisible = false">关闭</el-button>
                </span>
            </el-dialog>
        </div>
    </div> 
</template>
<script>
import apiBase from '../../apiBase';
import Page from '../../components/Page';
import { getJsonAndCheckSuccess, reportErrorMessage } from '../../ApiUtil';

export default {
  name: 'Risk',
  data() {
    return {
      totalScore: 0,
      totalValidScore: 0,
      tableData: [],
      form: {
        name: '',
        state: '',
      },
      dialogForm: {
        id: '',
        title: '',
        module: '',
        pattern: 'result',
        name: '',
        item: '',
        operator: '',
        val: '', // 值
        outcome: 1, // 结果
        symbol: 'increase', // 增加减少
        score: 0, // 分数
        state: 2, // 启用
        remarks: '',
      },
      riskModule: [],
      riskItems: [],
      pageCount: 0,
      currentPage: 1,
      pageSize: 20,
      isNew: true,
      isFirst: true,
      seen: false,
      dialogFormVisible: false,
      patterns: [{ id: 'result', name: '结果模式' }, { id: 'score', name: '评分模式' }],
      states: [{ id: 1, name: '禁用' }, { id: 2, name: '启用' }],
      outcomes: [{ id: 1, name: '通过' }, { id: 2, name: '不通过' }, { id: 3, name: '需人工审核' }],
      operators: [],
      symbols: [{ id: 'increase', name: '增加' }, { id: 'decrease', name: '减少' }],
    };
  },
  components: {
    Page,
  },
  mounted() {
    this.getData();
  },
  computed: {
    errTxt() {
      this.dialogForm.val = this.dialogForm.val.replace('，', ',');
      return '';
    },
  },
  methods: {
    filter() {
      this.getData();
    },
    handleCurrentChange(page) {
      this.currentPage = page;
      this.getData();
    },
    getData() {
      const params = {
        limit: this.pageSize,
        offset: this.pageSize * (this.currentPage - 1),
      };
      if (this.form.name !== '') {
        params.name = this.form.name;
      }
      if (this.form.state !== '') {
        params.state = this.form.state;
      }
      this.$http.get(`${apiBase}risk-rule`, { params })
        .then(getJsonAndCheckSuccess)
        .then((response) => {
          this.tableData = response.results;
          this.pageCount = response.count;
          this.riskModule = response.risk;
          this.totalScore = response.totalScore;
          this.totalValidScore = response.totalValidScore;
        }).catch(reportErrorMessage(this));
    },
    handleAdd() {
      this.clearForm();
      this.isFirst = false;
      this.dialogForm.title = '添加规则';
      this.dialogFormVisible = true;
      this.operators = [];
      this.riskItems = [];
    },
    handleSubmit() {
      if (this.dialogForm.operator === '<=AND<=') {
        this.dialogForm.val = this.dialogForm.val.replace('，', ','); // 半角逗号替代全角逗号
        if (!/^\d+,\d+$/.test(this.dialogForm.val)) {
          this.$message.error('当认证状态为<=AND<=时，需输入的值为用逗号隔开的区间值，如：1,100');
          return;
        }
        if (/^\d+,\d+$/.test(this.dialogForm.val)) {
          const arr = this.dialogForm.val.split(',');
          if (arr.length !== 2) {
            this.$message.error('有且只能有一个逗号存在');
            return;
          }
          if (Number(arr[0]) > Number(arr[1])) {
            this.$message.error('逗号前面的值应小于逗号后面的值如：1,100');
            return;
          }
        }
      }
      // 其他输入状态
      if (this.dialogForm.operator !== 'accordant' && this.dialogForm.operator !== 'disaccord') {
        if (this.dialogForm.val === '') {
          this.$message.error('当前所选择的认证状态对应的值不能为空');
          return;
        }
      }
      const params = {
        module: this.dialogForm.module,
        itemName: this.dialogForm.name,
        pattern: this.dialogForm.pattern,
        operator: this.dialogForm.operator,
        val: this.dialogForm.val,
        outcome: this.dialogForm.outcome,
        symbol: this.dialogForm.symbol,
        score: this.dialogForm.score,
        state: this.dialogForm.state,
        remarks: this.dialogForm.remarks,
      };
      this.$http.post(`${apiBase}add-risk`, params)
        .then(response => response.json())
        .then((json) => {
          if (json.status !== 'SUCCESS') {
            return Promise.reject(json.error_message);
          }
          this.$message({
            type: 'success',
            message: '添加成功！',
          });
          this.getData();
          this.dialogFormVisible = false;
          return {};
        }).catch(reportErrorMessage(this));
    },
    clearForm() {
      this.dialogForm = {
        module: '',
        pattern: '',
        name: '',
        item: '',
        operator: '',
        val: '', // 值
        outcome: 1, // 结果
        symbol: 'increase', // 增加减少
        score: 0, // 分数
        state: 2, // 启用禁用
        remarks: '',
      };
    },
    handleUpdate(row) {
      this.isFirst = true;
      this.riskItems = this.riskModule[row.module].items;
      this.dialogForm.title = '编辑规则';
      this.dialogFormVisible = true;
      this.dialogForm.id = row.id;
      this.dialogForm.module = row.module;
      this.dialogForm.pattern = row.pattern;
      this.dialogForm.name = `${row.item},${row.name}`;
      this.dialogForm.operator = row.operator;
      this.dialogForm.val = row.val;
      this.dialogForm.outcome = row.outcome;
      this.dialogForm.symbol = row.symbol;
      this.dialogForm.score = row.score;
      this.dialogForm.state = row.state;
      this.dialogForm.remarks = row.remarks;
      if (row.pattern === 'result') {
        this.operators = [{ id: 'accordant', name: '均一致' }, { id: 'disaccord', name: '不一致' }];
      } else if (row.pattern === 'score') {
        this.seen = true;
        this.operators = [{ id: '>', name: '>' }, { id: '<', name: '<' }, { id: '>=', name: '>=' }, { id: '<=', name: '<=' }, { id: '==', name: '==' }, { id: '!=', name: '!=' }, { id: '<=AND<=', name: '<=AND<=' }];
      }
    },
    handleSubmitUpdate() {
      if (this.dialogForm.operator === '<=AND<=') {
        this.dialogForm.val = this.dialogForm.val.replace('，', ','); // 半角逗号替代全角逗号
        if (!/^\d+,\d+$/.test(this.dialogForm.val)) {
          this.$message.error('当认证状态为<=AND<=时，需输入的值为用逗号隔开的区间值，如：1,100');
          return;
        }
        if (/^\d+,\d+$/.test(this.dialogForm.val)) {
          const arr = this.dialogForm.val.split(',');
          if (arr.length !== 2) {
            this.$message.error('有且只能有一个逗号存在');
            return;
          }
          if (Number(arr[0]) > Number(arr[1])) {
            this.$message.error('逗号前面的值应小于逗号后面的值如：1,100');
            return;
          }
        }
      }
      if (this.dialogForm.operator !== 'accordant' && this.dialogForm.operator !== 'disaccord') {
        if (this.dialogForm.val === '') {
          this.$message.error('当前所选择的认证状态对应的值不能为空');
          return;
        }
      }
      const params = {
        id: this.dialogForm.id,
        module: this.dialogForm.module,
        itemName: this.dialogForm.name,
        pattern: this.dialogForm.pattern,
        operator: this.dialogForm.operator,
        val: this.dialogForm.val,
        outcome: this.dialogForm.outcome,
        symbol: this.dialogForm.symbol,
        score: this.dialogForm.score,
        state: this.dialogForm.state,
        remarks: this.dialogForm.remarks,
      };
      this.$http.put(`${apiBase}update-risk`, params)
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
          this.dialogFormVisible = false;
          return {};
        }).catch(reportErrorMessage(this));
    },
    handleUpdateState(id) {
      this.$confirm('您确定执行此操作吗?', '提示', {
        type: 'warning',
      }).then(() => {
        this.$http.put(`${apiBase}update-risk-state/${id}`)
          .then(response => response.json())
          .then((json) => {
            if (json.status !== 'SUCCESS') {
              return Promise.reject(json.error_message);
            }
            this.$message({
              type: 'success',
              message: '操作成功！',
            });
            this.getData();
            return {};
          }).catch(reportErrorMessage(this));
      }).catch(() => {});
    },
    handleDel(id) {
      this.$confirm('您确定删除此条规则吗?', '提示', {
        type: 'warning',
      }).then(() => {
        this.$http.put(`${apiBase}del-risk-rule/${id}`)
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
    handleClose() {
      this.isNew = true;
    },
    clearFilter() {
      this.form = {
        state: '',
        title: '',
      };
      this.getData();
    },
    changeModule(v) {
      if (v && this.riskModule[v].items) {
        if (!this.isNew) {
          this.dialogForm.name = '';
        }
        this.isNew = false;
        this.riskItems = this.riskModule[v].items;
      }
    },
    changePattern(v) {
      if (!this.isFirst) { // 若是新开对话框导致模式的切换，认证状态不置空，保持原有的值
        this.dialogForm.operator = '';
      }
      this.isFirst = false;
      if (v === 'result') {
        this.dialogForm.val = '';
        this.seen = false;
        this.operators = [{ id: 'accordant', name: '均一致' }, { id: 'disaccord', name: '不一致' }];
      } else if (v === 'score') {
        this.seen = true;
        this.operators = [{ id: '>', name: '>' }, { id: '<', name: '<' }, { id: '>=', name: '>=' }, { id: '<=', name: '<=' }, { id: '==', name: '==' }, { id: '!=', name: '!=' }, { id: '<=AND<=', name: '<=AND<=' }];
      }
    },
    moduleFormatter(row) {
      return this.riskModule[row.module].description ? this.riskModule[row.module].description : '';
    },
    patternFormatter(row) {
      return this.patterns.find(v => v.id === row.pattern).name ? this.patterns.find(v => v.id === row.pattern).name : '';
    },
    outcomeFormatter(row) {
      return this.outcomes.find(v => v.id === row.outcome) ? this.outcomes.find(v => v.id === row.outcome).name : '';
    },
    stateFormatter(row) {
      switch (row.state) {
        case 1 :
          return '禁用';
        case 2 :
          return '启用';
        default:
          return '';
      }
    },
    authStateFormatter(row) {
      switch (row.operator) {
        case '>' :
        case '<' :
        case '>=' :
        case '<=' :
        case '==' :
        case '!=' :
        case '<=AND<=' :
          return `${row.operator};${row.val}`;
        case 'accordant':
          return '均一致';
        case 'disaccord':
          return '不一致';
        default:
          return '';
      }
    },
    scoreFormatter(row) {
      switch (row.symbol) {
        case 'increase' :
          return row.score;
        case 'decrease' :
          return 0 - Number(row.score);
        default:
          return row.score;
      }
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
