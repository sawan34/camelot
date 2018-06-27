#!/usr/bin/env bash

DIR=$(pwd)
FWDIR="$DIR/.framework"
TAGS=$1

if [[ -d "$FWDIR" ]]; then
	cd $FWDIR
	echo "Updating subsplit from origin"

	git fetch -q -t origin
	git checkout master
	git reset --hard origin/master

	cd $DIR
else
	git clone https://github.com/laravel/framework.git .framework
fi

for TAG in $TAGS
do
	if ! git -C "$FWDIR" show-ref --quiet --verify -- "refs/tags/${TAG}"
	then
		echo " - skipping tag '${TAG}' (does not exist)"
		continue
	fi

	LOCAL_TAG="${TAG}"
	echo "LOCAL_TAG="${LOCAL_TAG}""

	if git branch | grep "${LOCAL_TAG}$" >/dev/null
	then
		echo " - skipping tag '${TAG}' (already synced)"
		continue
	fi

	git -C "$FWDIR" checkout $TAG

	echo "Copying source..."
	mkdir -p $DIR/src/Illuminate/
	mkdir -p $DIR/src/Illuminate/Contracts
	mkdir -p $DIR/tests/

	cp -R $FWDIR/src/Illuminate/Container $DIR/src/Illuminate/
	cp -R $FWDIR/src/Illuminate/Contracts/Container $DIR/src/Illuminate/Contracts/
	cp -R $FWDIR/tests/Container $DIR/tests/

	echo "Commit"
	git add "src/*"
	git add "tests/*"

	git commit -m "$TAG"
	# git tag -a $TAG -m "$TAG"

	echo 'Done!'
done
