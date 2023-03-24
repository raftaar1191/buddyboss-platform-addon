#!/bin/bash
# Usage: ./buddyboss-platform-addon/init-plugin.sh

# Check for valid plugin name.
function valid_name () {
	valid="^[A-Z][A-Za-z0-9]*( [A-Z][A-Za-z0-9]*)*$"

	if [[ ! "$1" =~ $valid ]]; then
		return 1
	fi

	return 0
}

echo
echo "Hello, "$USER"."
echo
echo "This script will automatically generate a new plugin based on the scaffolding."
echo "The way it works is you enter a plugin name like 'Hello World' and the script "
echo "will create a directory 'hello-world' in the current working directory, or one "
echo "directory up if called from the plugin root, all while performing substitutions "
echo "on the 'buddyboss-platform-addon' scaffolding plugin."
echo

echo -n "Enter your plugin name and press [ENTER]: "
read name

# Validate plugin name.
if ! valid_name "$name"; then
	echo "Malformed name '$name'. Please use title case words separated by spaces. No hyphens. For example, 'Hello World'."
	echo
	echo -n "Enter a valid plugin name and press [ENTER]: "
	read name

	if ! valid_name "$name"; then
		echo
		echo "The name you entered is invalid, rage quitting."
		exit 1
	fi
fi

slug="$( echo "$name" | tr '[:upper:]' '[:lower:]' | sed 's/ /-/g' )"
prefix="$( echo "$name" | tr '[:upper:]' '[:lower:]' | sed 's/ /_/g' )"
namespace="$( echo "$name" | sed 's/ //g' )"
class="$( echo "$name" | sed 's/ /_/g' )"

="$slug"

echo
echo "The Organization name will be converted to lowercase for use in the repository "
echo "path (i.e. BuddyBoss becomes buddyboss)."
echo -n "Enter your GitHub organization name, and press [ENTER]: "
read org

org_lower="$( echo "$org" | tr '[:upper:]' '[:lower:]' )"

echo
echo -n "Do you want to prepend 'bb-' to your repository name? [Y/N]: "
read prepend

if [[ "$prepend" != Y ]] && [[ "$prepend" != y ]]; then
	echo
	echo -n "Do you want to append '-bb' to your repository name? [Y/N]: "
    read append

	if [[ "$append" == Y ]] || [[ "$append" == y ]]; then
		repo="${slug}-bb"
	fi
else
	repo="bb-${slug}"
fi

# echo
# echo -n "Do you want to make the initial commit? [Y/N]: "
# read commit

# if [[ "$commit" == Y ]] || [[ "$commit" == y ]]; then
# 	echo
# 	echo -n "Do you want to push the plugin to your GitHub repository? [Y/N]: "
# 	read push
# fi

echo
echo -n "Do you want to install the dependencies in the new plugin? [Y/N]: "
read deps

echo

cwd="$(pwd)"
cd "$(dirname "$0")"
src_repo_path="$(pwd)"
cd "$cwd"

if [[ -e $( basename "$0" ) ]]; then
    echo
	echo "Moving up one directory outside of 'buddyboss-platform-addon'"
	cd ..
fi

if [[ -e "$slug" ]]; then
    echo
	echo "The $slug directory already exists"
	exit 1
fi

echo

git clone "$src_repo_path" "$repo"

cd "$repo"

git mv buddyboss-platform-addon.php "$slug.php"

git grep -lz "buddyboss%2Fbuddyboss-platform-addon" | xargs -0 sed -i '' -e "s|push%2Fbuddyboss-platform-addon|$org_lower%2F$repo|g"
git grep -lz "push/buddyboss-platform-addon" | xargs -0 sed -i '' -e "s|push/buddyboss-platform-addon|$org_lower/$repo|g"
git grep -lz "buddyboss-platform-addon" | xargs -0 sed -i '' -e "s/buddyboss-platform-addon/$repo/g"
git grep -lz "BuddyBoss Platform Add-on" | xargs -0 sed -i '' -e "s/Foo Bar/$name/g"
git grep -lz "buddyboss-platform-addon" | xargs -0 sed -i '' -e "s/buddyboss-platform-addon/$slug/g"
git grep -lz "buddyboss_platform_addon" | xargs -0 sed -i '' -e "s/buddyboss_platform_addon/$prefix/g"
git grep -lz "BuddyBossPlatformAddon" | xargs -0 sed -i '' -e "s/BuddyBossPlatformAddon/$namespace/g"
git grep -lz "BuddyBoss_Platform_Addon" | xargs -0 sed -i '' -e "s/BuddyBoss_Platform_Addon/$class/g"

# Clean slate.
rm -rf .git
rm -rf node_modules
rm -rf vendor
rm -f init-plugin.sh
rm -f composer.lock
rm -f package-lock.json

# Setup Git.
git init
git add .
git remote add origin "git@github.com:$org_lower/$repo.git"

# Install dependencies.
if [[ "$deps" == Y ]] || [[ "$deps" == y ]]; then
	npm install
fi

echo
echo "Plugin is located at:"
pwd