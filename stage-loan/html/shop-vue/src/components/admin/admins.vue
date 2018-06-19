<template>
	<style>
			*{border:0;padding:0;margin:0;}
			table{width:80%;margin:0 auto;}
			table tr:nth-child(odd){background:gainsboro;}
			table tr td{text-align: center;}
			button{width:120px;background:#ccffcc;color:darkblue;border-radius:6px;}
		</style>
	<table>
			<tr>
				<td>id</td>
				<td>用户名</td>
				<td>真实姓名</td>
				<td>登陆IP</td>
				<td>时间</td>
				<td>操作</td>
			</tr>
			<tr>
				<td>001</td>
				<td>test</td>
				<td>测试用</td>
				<td>0010001</td>
				<td>20170715</td>
				<td><button>删除</button></td>
			</tr>
			<tr>
				<td>001</td>
				<td>test</td>
				<td>测试用</td>
				<td>0010001</td>
				<td>20170715</td>
				<td><button>删除</button></td>
			</tr>
			<tr>
				<td>001</td>
				<td>test</td>
				<td>测试用</td>
				<td>0010001</td>
				<td>20170715</td>
				<td><button>删除</button></td>
			</tr>
			<tr>
				<td>001</td>
				<td>test</td>
				<td>测试用</td>
				<td>0010001</td>
				<td>20170715</td>
				<td><button>删除</button></td>
			</tr>
		</table>
</template>

<script>
	export default{
		
	}
</script>