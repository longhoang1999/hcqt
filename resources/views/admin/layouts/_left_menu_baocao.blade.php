<style>
	.img-rounded{
		display: none;
	}
	#side-menu{
		padding: 0;
	}
	#side-menu li a{
		padding: 10px 0;
		padding-left: 15px;
		display: flex;
		align-items: center;

		color: white;
	}
	#side-menu li a:hover{
		background-color: red;
	}
	.nav-label{
		display: block;
		margin-left: 5px;
	}
</style>

@include("admin.project.ExternalReview.include.sidebar")