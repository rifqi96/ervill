<div>
	<ol>		
		<li>
			<p>Here are the list of API endpoints of http://rev-ervill.esy.es/api:</p>
			<ul>
				<li><b>GET METHOD:</b></li>
				<ul>
					<li><b>Public / Don't need access_token:</b></li>					
					<li>/users = Get all users list</li>
					<li>/users/{id} = Get specific user data</li>
					<br>

					<li><b>Private / Need access_token (OAuth2):</b></li>
					<li>/orderCustomers = Get all order customers list</li>
					<li>/orderCustomers/{id} = Get specific order customer data</li>
				</ul>
			</ul>
		</li>

		<li>
			<p>How to get the access_token?</p>			
			<p>=> Send a <b>POST</b> method to <b>http://rev-ervill.esy.es/oauth/token</b> with <b>Request Body</b> of</p>			
			<ol>
				<li>grant_type: password</li>
				<li>client_id: 2</li>
				<li>client_secret: 8rBDxxrOUa6U5M5yNeUgAXXQfU42wonF6QMsFiDU</li>
				<li>username: apitester@ervill.net</li>
				<li>password: apitester</li>
				<li>scope: </li>
			</ol>
			<p>After that you will get an access_token, copy that token</p>
		</li>

		<li>
			<p><b>Private</b> API endpoints must use following key-value in the <b>Headers</b> :</p>
			<ul>
				<li>Accept: application/json</li>
			</ul>
			<ul>
				<li>Authorization: Bearer *YOUR-ACCESS-TOKEN*</li>
			</ul>
		</li>
	</ol>
</div>