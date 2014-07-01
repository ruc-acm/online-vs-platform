#include <string>
#include <vector>
#include <boost/python/list.hpp>
using std::string;
using std::vector;
using boost::python::list;
typedef list StrVector;
class Judge
{
	protected:		
		int won;
	public:
		string player_name;
		void started();
		void dead();
		int before_write();
		StrVector on_write();
		int after_read(string line);
		int victorious();
		Judge(string name);
};

