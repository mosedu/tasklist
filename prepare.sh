#!/bin/sh

curdir=`pwd`
runtime_dir="${curdir}/runtime"
asset_dir="${curdir}/web/assets"
uploadf_dir="${curdir}/web/upload"

make_dirs="${runtime_dir} ${asset_dir} ${uploadf_dir}"

www_dir="www"
web_dir="web"

for sdir in $make_dirs ; do
    echo -n "${sdir} : "
    if [ ! -d ${sdir} ]; then
        mkdir ${sdir}
        chmod 777 ${sdir}
        echo "make"
    else
        echo "exists"
    fi
done

if [ ! -L ${www_dir} ]; then
    if [ -d ${www_dir} ]; then
        rm -r ${www_dir}
    fi
    ln -s ${web_dir} ${www_dir}
    echo "make link ${web_dir} -> ${www_dir}"
fi

cd vendor
if [ ! -L bower ]; then
    ln -s bower-asset bower
fi
