plugin=GiteaWebhook
version=1.2.0
all:
	@ echo "Build archive for plugin ${plugin} version=${version}"
	@ git archive HEAD --prefix=${plugin}/ --format=zip -o ${plugin}-${version}.zip
