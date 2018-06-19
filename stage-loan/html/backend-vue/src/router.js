import Vue from 'vue';
import Router from 'vue-router';

import AdminList from './pages/admin/List';
import AdminForm from './pages/admin/Form';
import AssignForm from './pages/admin/AssignForm';
import RoleList from './pages/role/List';
import RoleForm from './pages/role/Form';
import AuthList from './pages/auth/List';
import AuthForm from './pages/auth/Form';
import UserList from './pages/user/List';
import UserLoan from './pages/user/Loan';
import BasicDetail from './pages/user/BasicDetail';
import ProductList from './pages/product/List';
import ProductForm from './pages/product/Form';
import LoanList from './pages/loan/List';
import PreliminaryList from './pages/loan/PreliminaryList';
import ReviewsList from './pages/loan/ReviewsList';
import RepaymentsList from './pages/loan/RepaymentsList';
import PreliminaryForm from './pages/loan/PreliminaryForm';
import ReviewsForm from './pages/loan/ReviewsForm';
import LoanDetail from './pages/loan/LoanDetail';
import IdentityCardList from './pages/ident/IdentityCardList';
import AuthCenterList from './pages/ident/AuthCenterList';
import AuthMobileList from './pages/ident/AuthMobileList';
import AuthMobileReportDetail from './pages/ident/ReportDetail';
import AuthProfileList from './pages/ident/AuthProfileList';
import AuthBankList from './pages/ident/AuthBankList';
import AuthJdList from './pages/ident/AuthJdList';
import AuthJdDetail from './pages/ident/JdDetail';
import AuthTaobaoList from './pages/ident/AuthTaobaoList';
import AuthTaobaoDetail from './pages/ident/TaobaoDetail';
import AuthEduList from './pages/ident/AuthEduList';
import AuthEduDetail from './pages/ident/EduDetail';
import AuthBillList from './pages/ident/AuthBillList';
import AuthBillDetail from './pages/ident/BillDetail';
import AuthEbankList from './pages/ident/AuthEbankList';
import AuthEbankDetail from './pages/ident/EbankDetail';
import OverdueList from './pages/overdue/OverdueList';
import AssignedList from './pages/overdue/AssignedList';
import UrgencyList from './pages/overdue/UrgencyList';
import FeedbackList from './pages/content/FeedbackList';
import MessageList from './pages/content/MessageList';
import ArticleList from './pages/content/ArticleList';
import ArticleForm from './pages/content/ArticleForm';
import PaymentList from './pages/payment/List';
import Statistic from './pages/statistic/Statistic';
import QuotaList from './pages/quota/List';
import AuditList from './pages/quota/AuditList';
import AuditQuotaLog from './pages/quota/AuditQuotaLogList';
import Risk from './pages/system/Risk';
import RiskRuleList from './pages/system/RiskRuleList';
import CreditGradeList from './pages/system/CreditGradeList';
import IncreaseQuotaList from './pages/ident/IncreaseQuotaList';
import DailyStatistics from './pages/statistic/daily';
import LoanStatistics from './pages/statistic/LoanStatistics';
import OverdueStatistics from './pages/statistic/OverdueStatistics';
import RepaymentStatistics from './pages/statistic/RepaymentStatistics';
import UrgeStatistics from './pages/statistic/UrgeStatistics';
import ShopCategoryList from './pages/category/ShopCategoryList';
import ProductCategoryList from './pages/category/ProductCategoryList';
import ShopApplyForm from './pages/shop/ShopApplyForm';
import ShopList from './pages/shop/ShopList';
import ShopAuditForm from './pages/shop/ShopAuditForm';
import ShopDetail from './pages/shop/ShopDetail';
import ShopSettledList from './pages/shop/ShopSettledList';
import ShopQuotaList from './pages/quotaShop/List';
import ShopQuotaAuditList from './pages/quotaShop/AduitList';
import ShopQuotaAuditLogList from './pages/quotaShop/AuditQuotaLogList';
import AuthHouseFundList from './pages/ident/AuthHouseFundList';
import AuthSocialSecurityList from './pages/ident/AuthSocialSecurityList';
import AuthCreditList from './pages/ident/AuthCreditList';
import HouseFundDetail from './pages/ident/HouseFundDetail';
import SocialSecurityDetail from './pages/ident/SocialSecurityDetail';
import CreditDetail from './pages/ident/CreditDetail';
import GrantList from './pages/loan/GrantListe';

Vue.use(Router);

const router = new Router({
  routes: [
    {
      path: '/admins',
      component: AdminList,
      meta: { title: '管理员列表' },
    },
    {
      path: '/add-admin',
      component: AdminForm,
      meta: { title: '添加管理员' },
    },
    {
      path: '/update-admin/:id',
      component: AdminForm,
      meta: { title: '编辑管理员' },
    },
    {
      path: '/roles',
      component: RoleList,
      meta: { title: '角色列表' },
    },
    {
      path: '/add-role',
      component: RoleForm,
      meta: { title: '添加角色' },
    },
    {
      path: '/update-role/:name',
      component: RoleForm,
      meta: { title: '编辑角色' },
    },
    {
      path: '/assign-auth/:name',
      component: AssignForm,
      meta: { title: '权限分配' },
    },
    {
      path: '/auths',
      component: AuthList,
      meta: { title: '权限列表' },
    },
    {
      path: '/add-auth',
      component: AuthForm,
      meta: { title: '添加权限' },
    },
    {
      path: '/update-auth/:name',
      component: AuthForm,
      meta: { title: '编辑权限' },
    },
    {
      path: '/users',
      component: UserList,
      meta: { title: '用户列表' },
    },
    {
      path: '/user-detail/:id',
      component: BasicDetail,
      meta: { title: '用户详情' },
    },
    {
      path: '/user-loan/:id',
      component: UserLoan,
      meta: { title: '借款记录' },
    },
    {
      path: '/products',
      component: ProductList,
      meta: { title: '产品配置列表' },
    },
    {
      path: '/add-product',
      component: ProductForm,
      meta: { title: '添加产品配置' },
    },
    {
      path: '/update-product/:id',
      component: ProductForm,
      meta: { title: '编辑产品配置' },
    },
    {
      path: '/borrows',
      component: LoanList,
      meta: { title: '借款列表' },
    },
    {
      path: '/checks',
      component: PreliminaryList,
      meta: { title: '初审列表' },
    },
    {
      path: '/reviews',
      component: ReviewsList,
      meta: { title: '复审列表' },
    },
    {
      path: '/repayments',
      component: RepaymentsList,
      meta: { title: '还款列表' },
    },
    {
      path: '/checks-audit/:id',
      component: PreliminaryForm,
      meta: { title: '初审审核' },
    },
    {
      path: '/reviews-audit/:id',
      component: ReviewsForm,
      meta: { title: '复审审核' },
    },
    {
      path: '/loan-detail/:id',
      component: LoanDetail,
      meta: { title: '借款详情' },
    },
    {
      path: '/identity',
      component: IdentityCardList,
      meta: { title: '身份认证列表' },
    },
    {
      path: '/auth-center',
      component: AuthCenterList,
      meta: { title: '认证中心列表' },
    },
    {
      path: '/auth-mobile',
      component: AuthMobileList,
      meta: { title: '手机认证列表' },
    },
    {
      path: '/mobile-detail/:id',
      component: AuthMobileReportDetail,
      meta: { title: '手机运营商报告' },
    },
    {
      path: '/auth-profile',
      component: AuthProfileList,
      meta: { title: '个人信息列表' },
    },
    {
      path: '/auth-bank',
      component: AuthBankList,
      meta: { title: '银行卡认证列表' },
    },
    {
      path: '/auth-jd',
      component: AuthJdList,
      meta: { title: '京东认证列表' },
    },
    {
      path: '/jd-detail/:id',
      component: AuthJdDetail,
      meta: { title: '京东详情' },
    },
    {
      path: '/auth-taobao',
      component: AuthTaobaoList,
      meta: { title: '淘宝认证列表' },
    },
    {
      path: '/taobao-detail/:id',
      component: AuthTaobaoDetail,
      meta: { title: '淘宝详情' },
    },
    {
      path: '/auth-edu',
      component: AuthEduList,
      meta: { title: '学历认证列表' },
    },
    {
      path: '/edu-detail/:id',
      component: AuthEduDetail,
      meta: { title: '学历详情' },
    },
    {
      path: '/auth-bill',
      component: AuthBillList,
      meta: { title: '信用卡账单列表' },
    },
    {
      path: '/bill-detail/:id',
      component: AuthBillDetail,
      meta: { title: '信用卡账单详情' },
    },
    {
      path: '/auth-ebank',
      component: AuthEbankList,
      meta: { title: '网银流水列表' },
    },
    {
      path: '/ebank-detail/:id',
      component: AuthEbankDetail,
      meta: { title: '网银流水详情' },
    },
    {
      path: '/overdues',
      component: OverdueList,
      meta: { title: '分配详情' },
    },
    {
      path: '/urge-lists',
      component: AssignedList,
      meta: { title: '已分配详情' },
    },
    {
      path: '/urgency',
      component: UrgencyList,
      meta: { title: '我的催收列表' },
    },
    {
      path: '/feedback',
      component: FeedbackList,
      meta: { title: '意见反馈列表' },
    },
    {
      path: '/messages',
      component: MessageList,
      meta: { title: '短信发送记录' },
    },
    {
      path: '/article',
      component: ArticleList,
      meta: { title: '文章列表' },
    },
    {
      path: '/article-add',
      component: ArticleForm,
      meta: { title: '添加文章' },
    },
    {
      path: '/article-update/:id',
      component: ArticleForm,
      meta: { title: '编辑文章' },
    },
    {
      path: '/confirm-list',
      component: PaymentList,
      meta: { title: '付款确认' },
    },
    {
      path: '/statistics',
      component: Statistic,
      meta: { title: '统计信息' },
    },
    {
      path: '/quotas',
      component: QuotaList,
      meta: { title: '额度列表' },
    },
    {
      path: '/quota-audit',
      component: AuditList,
      meta: { title: '额度审核列表' },
    },
    {
      path: '/quota-log',
      component: AuditQuotaLog,
      meta: { title: '额度记录列表' },
    },
    {
      path: '/risk',
      component: Risk,
      meta: { title: '信用分表字段' },
    },
    {
      path: '/risk-rule',
      component: RiskRuleList,
      meta: { title: '信用分规则配置' },
    },
    {
      path: '/credit-set',
      component: CreditGradeList,
      meta: { title: '信用分设置' },
    },
    {
      path: '/increase-quota',
      component: IncreaseQuotaList,
      meta: { title: '提升额度列表' },
    },
    {
      path: '/daily',
      component: DailyStatistics,
      meta: { title: '每日统计' },
    },
    {
      path: '/loan-statistics',
      component: LoanStatistics,
      meta: { title: '借款统计' },
    },
    {
      path: '/overdue-statistics',
      component: OverdueStatistics,
      meta: { title: '逾期统计' },
    },
    {
      path: '/repayment-statistics',
      component: RepaymentStatistics,
      meta: { title: '还款统计' },
    },
    {
      path: '/urge-statistics',
      component: UrgeStatistics,
      meta: { title: '催收统计' },
    },
    {
      path: '/category-shop',
      component: ShopCategoryList,
      meta: { title: '商户分类' },
    },
    {
      path: '/category-pro',
      component: ProductCategoryList,
      meta: { title: '商品分类' },
    },
    {
      path: '/shop',
      component: ShopList,
      meta: { title: '商户列表' },
    },
    {
      path: '/shop-apply',
      component: ShopApplyForm,
      meta: { title: '商户申请' },
    },
    {
      path: '/shop-update/:id',
      component: ShopApplyForm,
      meta: { title: '编辑商户申请' },
    },
    {
      path: '/shop-audit/:id',
      component: ShopAuditForm,
      meta: { title: '商户审核' },
    },
    {
      path: '/shop-detail/:id',
      component: ShopDetail,
      meta: { title: '商户详情' },
    },
    {
      path: '/shop-settled',
      component: ShopSettledList,
      meta: { title: '商户入驻' },
    },
    {
      path: '/shop-quota-list',
      component: ShopQuotaList,
      meta: { title: '商户额度管理' },
    },
    {
      path: '/shop-quota-audit-list',
      component: ShopQuotaAuditList,
      meta: { title: '商户额度审核列表' },
    },
    {
      path: '/shop-quota-log-list',
      component: ShopQuotaAuditLogList,
      meta: { title: '商户额度记录' },
    },
    {
      path: '/auth-house-fund',
      component: AuthHouseFundList,
      meta: { title: '公积金认证' },
    },
    {
      path: '/auth-social-security',
      component: AuthSocialSecurityList,
      meta: { title: '社保认证' },
    },
    {
      path: '/auth-credit',
      component: AuthCreditList,
      meta: { title: '央行征信认证' },
    },
    {
      path: '/auth-credit',
      component: AuthCreditList,
      meta: { title: '央行征信认证' },
    },
    {
      path: '/house-fund-detail/:id',
      component: HouseFundDetail,
      meta: { title: '公积金详情' },
    },
    {
      path: '/social-security-detail/:id',
      component: SocialSecurityDetail,
      meta: { title: '社保详情' },
    },
    {
      path: '/credit-detail/:id',
      component: CreditDetail,
      meta: { title: '央行征信详情' },
    },
    {
      path: '/grant',
      component: GrantList,
      meta: { title: '放款管理' },
    },
  ],
});

module.exports = router;

