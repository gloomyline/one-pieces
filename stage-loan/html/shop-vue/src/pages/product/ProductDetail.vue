<template>
    <div style="background-color: #f2f2f2;padding: 20px;overflow: auto">
        <el-button type="success" style="margin-bottom: 5px" @click.native="$router.back(-1)">返回上一页</el-button>
        <div style="width:1000px;border-left: solid 1px #868686;border-top: solid 2px #868686;border-right: solid 2px #868686;border-bottom: solid 2px #868686">
            <!--商品信息：begin-->
            <el-row style="">
                <el-col :span="6" class="audit-div">商品名称</el-col><el-col :span="18" class="audit-div">{{ product.title }}</el-col>
            </el-row>
            <el-row style="border-top: 1px solid">
                <el-col :span="6" class="audit-div">商品货号</el-col><el-col :span="6" class="audit-div">{{ product.no }}</el-col>
                <el-col :span="6" class="audit-div">商品分类</el-col><el-col :span="6" class="audit-div">{{ product.category_name }}</el-col>
            </el-row>
            <el-row style="border-top: 1px solid">
                <el-col :span="6" class="audit-div">排序</el-col><el-col :span="18" class="audit-div" style="text-align: left;padding-left: 20px">{{ product.sort }}</el-col>
            </el-row>
            <!--商品图片-->
            <el-row style="border-top: 1px solid">
                <el-col :span="6" class="audit-div" style="height: 240px">商品图片</el-col>
                <el-col :span="18" class="audit-div" style="height: 240px; overflow: auto">
                    <el-col :span="8" v-for=" pic in product.picArr">
                        <div style="padding-top: 10px">
                            <el-button type="text" @click.native="handleShowImg(pic)">
                                <div>
                                    <img :src="pic" class="image audit-img">
                                </div>
                            </el-button>
                        </div>
                    </el-col>
                </el-col>
            </el-row>
            <!--商品规格-->
            <el-row style="border-top: 1px solid">
                <el-col :span="6" class="audit-div" style="height: 240px">商品规格</el-col>
                <el-col :span="18" align="center" class="audit-div" style="height: 240px; overflow: auto">
                    <el-table :data="product.specList"  style="width: 100%;" align="center">
                        <el-table-column type="index" align="center"></el-table-column>
                        <el-table-column property="spec" label="规格" align="center"></el-table-column>
                        <el-table-column property="price" label="价格（元）" align="center"></el-table-column>
                        <el-table-column property="stock" label="库存" align="center"  ></el-table-column>
                    </el-table>
                </el-col>
            </el-row>
            <!--产品介绍-->
            <el-row style="border-top: 1px solid">
                <el-col :span="6" class="audit-div" style="height: 400px">产品介绍</el-col>
                <el-col :span="18" class="audit-div" style="height: 400px; overflow: auto">
                  <div v-html="product.intro" style="text-align: left"></div>
                </el-col>
            </el-row>
            <!--规格参数-->
            <el-row style="border-top: 1px solid">
                <el-col :span="6" class="audit-div" style="height: 400px">规格参数</el-col>
                <el-col :span="18" class="audit-div" style="height: 400px; overflow: auto;">
                    <div v-html="product.spec" style="text-align: left"></div>
                </el-col>
            </el-row>
            <!--售后-->
            <el-row style="border-top: 1px solid">
                <el-col :span="6" class="audit-div" style="height: 400px">售后</el-col>
                <el-col :span="18" class="audit-div" style="height: 400px; overflow: auto;">
                    <div v-html="product.service" style="text-align: left"></div>
                </el-col>
            </el-row>
            <!--审核信息-->
            <el-row>
                <el-col :span="24"  class="audit-div" style="border-top: 1px solid #868686"><h4>审核信息</h4></el-col>
            </el-row>
            <el-row style="border-top: 1px solid" v-if="product.state !== 0">
                <el-col :span="6" class="audit-div">审核结果</el-col><el-col :span="6" class="audit-div" v-text="stateFormatter(product.state)"></el-col>
                <el-col :span="6" class="audit-div">是否上架</el-col><el-col :span="6" class="audit-div" v-text="onSaleFormatter(product.on_sale)"></el-col>
            </el-row>
            <el-row style="border-top: 1px solid" v-if="product.state !== 0">
                <el-col :span="6" class="audit-div" style="height: 100px">审核意见</el-col>
                <el-col :span="18" class="audit-div" style="height: 100px; overflow: auto; text-align: left">{{ product.opinion }}</el-col>
            </el-row>
            <el-row  v-if="product.state === 0">
                <el-col :span="24"  class="audit-div" style="border-top: 1px solid #868686">商品尚未审核~~~</el-col>
            </el-row>
        </div>
        <!--图片放大：begin-->
        <el-dialog :visible.sync="dialogVisible" align="center" width="80%">
            <img :src="dialogImageUrl" alt="">
        </el-dialog>
        <!--图片放大：end-->
    </div>

</template>
<script>
  import apiBase from '../../apiBase';

  export default {
    name: 'productDetail',
    data() {
      return {
        product: {
          title: '',
          no: '',
          category_name: '',
          sort: '',
          picArr: [],
          specList: [],
          intro: '',
          spec: '',
          service: '',
          state: '',
        },
        shop_name: '',
        dialogVisible: false,
        dialogImageUrl: '',
      };
    },
    mounted() {
      this.getData();
    },
    methods: {
      getData() {
        this.$http.get(`${apiBase}detail/${this.$route.params.id}`)
          .then(response => response.json())
          .then((json) => {
            if (json.status === 'SUCCESS') {
              this.product = json.result;
            }
          }).catch(() => {
            this.$message('系统错误');
          });
      },
      handleShowImg(url) {
        this.dialogVisible = true;
        this.dialogImageUrl = url;
      },
      stateFormatter(v) {
        switch (v) {
          case 1 : return '通过';
          case 2 : return '未通过';
          default: return '';
        }
      },
      onSaleFormatter(v) {
        switch (v) {
          case 0 : return '下架';
          case 1 : return '上架';
          default: return '';
        }
      },
    },
  };
</script>

<style>
    .audit-div{
        min-height: 41px;
        margin: auto;
        text-align: center;
        line-height: 40px;
        border-left: 1px solid #868686;
    }
    .audit-img{
        width: 200px;
        height: 200px;
    }
</style>