#include <boost/python/module.hpp>
#include <boost/python/def.hpp>
#include <boost/python/class.hpp>
#include "judge.h"
#include <cstdio>
using namespace boost::python;
using boost::python::list;

Judge::Judge(string name)
{
	this->player_name = name;
	this->won = 0;
}

void Judge::started()
{
	fprintf(stderr , "%s started.\n" , this->player_name.c_str());
}

void Judge::dead()
{
	fprintf(stderr , "%s dead.\n" , this->player_name.c_str());	
}

int Judge::before_write()
{
	return 0;
}

StrVector Judge::on_write()
{
	vector<string> l;
	fprintf(stderr , "wrote to %s: %s.\n" , this->player_name.c_str() , "1 1");	
	l.push_back("1 1");
	list ret;
	for (vector<string>::iterator it = l.begin() ; it != l.end() ; ++it)
		ret.append(*it);
	return ret;
}

int Judge::after_read(string line)
{
	fprintf(stderr , "read from %s: %s.\n" , this->player_name.c_str() , line.c_str());	
	if (line == "2")
		return 0;
	else
	{
		this->won = 0;
		return 1;
	}
}

int Judge::victorious()
{
	return this->won;
}

BOOST_PYTHON_MODULE(judge_ext)
{
    class_<Judge>("Judge" , init<string>())
	.def("started", &Judge::started)
	.def("dead", &Judge::dead)
	.def("before_write", &Judge::before_write)
	.def("on_write", &Judge::on_write)
	.def("after_read", &Judge::after_read)
	.def("victorious", &Judge::victorious)
	.def_readwrite("player_name", &Judge::player_name);
}

