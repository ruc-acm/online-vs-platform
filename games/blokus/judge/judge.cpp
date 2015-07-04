#include <algorithm>
#include <vector>
#include <iostream>
#include <sstream>
#include <boost/python/module.hpp>
#include <boost/python/def.hpp>
#include <boost/python/class.hpp>
#include "judge.h"

using boost::python::list;
using std::vector;

vector<string> replay;
struct program
{
	char * file;
	pid_t pid;
	bool running;
	int score;
	void start(char* s)
	{
		this -> file = strdup(s);
		this -> score = 0;
	}
};

struct program* player1 = new program();
struct program* player2 = new program();

struct
{
	int num;
	struct
	{
		unsigned x,y;
	} box[5];
} block[30];

struct board
{
	bool over;
	int victor;
	int last_move;
	int pass_num;
	unsigned color[30][30];
	bool used[5][30];
	bool first[5];
	int tell;
	string last_command;
	void start()
	{
		this -> over = false;
		this -> victor = 0;
		this -> last_move = 0;
		memset(this -> color, 0, sizeof(this -> color));
		memset(this -> used, 0, sizeof(this -> used));
		memset(this -> first,1, sizeof(this -> first));
		this -> pass_num = 0;
		this -> tell = 0;
		FILE* data = fopen("block.ini","r");
		for (int i = 1; i <= 21; i++)
		{
			fscanf(data,"%d",&block[i].num);
			for (int j = 0; j < block[i].num; j++)
				fscanf(data,"%d%d",&block[i].box[j].x,&block[i].box[j].y);
		}
		fclose(data);
		this->last_command = "0 0 0 0 0 0 0";
		time_t tim;
		time(&tim);
		struct tm * place = localtime(&tim);
		char now[80];
		strftime(now,79,"%Y-%m-%d  %H:%M:%S:  ",place);
		FILE* replay = fopen("replay.txt","a");
		fprintf(replay,"%s",now);
		fprintf(replay,"start judge\n");
		fclose(replay);
	}
	bool outIt(int x,int y)
	{
		if (x<1 || x>20)
			return true;
		if (y<1 || y>20)
			return true;
		return false;
	}
	void Rotation(int &addx,int &addy,int d)
	{
		if (d == 1)
		{
			std::swap(addx,addy);
			addy = - addy;
		};
		if (d == 2)
		{
			addx = - addx;
			addy = - addy;
		};
		if (d == 3)
		{
			std::swap(addx,addy);
			addx = - addx;
		};
	}
	bool isLegalful(int ID,int x,int y,int block_id,int l,int v,int d)
	{
		if (ID < 1 || ID > 4) return false;
		if (!block_id) return true;		
		if (this -> last_move == 4 && ID != 1) return false;
		if (this -> last_move <  4 && ID != this -> last_move +1) return false;
		if (outIt(x,y)) return false;
		if (block_id < 0 || block_id > 21) return false;
		if (l > 1 || l < 0) return false;
		if (v > 1 || v < 0) return false;
		if (d < 0 || d > 3) return false;
		if (this -> used[ID][block_id]) return false;
		bool flag = false;
		for (int i = 0; i < block[block_id].num; i++)
		{
			int addx = block[block_id].box[i].x;
			if (l) addx = -addx;
			int addy = block[block_id].box[i].y;
			if (v) addy = -addy;
			Rotation(addx,addy,d);
			if (outIt(x+addx,y+addy))
				return false;
			if (this -> color[x+addx][y+addy])
				return false;
			if (ID == this -> color[x+addx-1][y+addy])
				return false;
			if (ID == this -> color[x+addx][y+addy+1])
				return false;
			if (ID == this -> color[x+addx+1][y+addy])
				return false;
			if (ID == this -> color[x+addx][y+addy-1])
				return false;
			if (ID == this -> color[x+addx-1][y+addy-1])
				flag = true;
			if (ID == this -> color[x+addx-1][y+addy+1])
				flag = true;
			if (ID == this -> color[x+addx+1][y+addy-1])
				flag = true;
			if (ID == this -> color[x+addx+1][y+addy+1])
				flag = true;
			if (this -> first[ID])
			{
				if (ID == 1 && x+addx == 1 && y+addy == 1)
					this -> first[ID] = false;
				if (ID == 2 && x+addx == 1 && y+addy == 20)
					this -> first[ID] = false;
				if (ID == 3 && x+addx == 20 && y+addy == 20)
					this -> first[ID] = false;
				if (ID == 4 && x+addx == 20 && y+addy == 1)
					this -> first[ID] = false;
				if (!this -> first[ID])
					flag = true;
			}
		}
		return flag;
	}

	static bool parse_input(const string &s, int &ID, int &x, int &y , int &block_id,int &l ,int  &v ,int &d)
	{
		using std::stringstream;
		stringstream ss(s);
		ss >> ID >> x >> y >> block_id >> l >> v >> d;
		return ss.good();
	}

	static bool normalize_input(string &s) {
		int ID,x,y,block_id,l,v,d;
		if (!parse_input(s, ID , x, y, block_id , l , v , d))
			return false;
		using std::stringstream;
		stringstream ss;
		ss << ID << ' ' << x << ' ' << y << ' ' << block_id << ' ' << l << ' ' << v << ' ' << d << endl;
		s = ss.str();
		return true;
	}

	bool judge_pass(const string &s)
	{
		int ID,x,y,block_id,l,v,d;
		board::parse_input(s, ID, x, y, block_id, l, v, d);
		return isLegalful(ID,x,y,block_id,l,v,d);
	}

	void addGamebox(const string &s)
	{
		int ID,x,y,block_id,l,v,d;
		board::parse_input(s, ID, x, y, block_id, l, v, d);
		if (block_id)
			this -> used[ID][block_id] = true;
		if (!block_id)
			this -> pass_num++;
		else
			this -> pass_num = 0;
		if (block_id)
			for (int i = 0; i < block[block_id].num; i++)
			{
				int addx = block[block_id].box[i].x;
				if (l) addx = -addx;
				int addy = block[block_id].box[i].y;
				if (v) addy = -addy;
				Rotation(addx,addy,d);
				this -> color[x+addx][y+addy] = ID;
			}
		this -> last_move = ID;
		if (this -> pass_num == 4)
		{
			this -> over = true;
			int player_score[5];
			memset(player_score,0,sizeof(player_score));
			for (int i = 1; i <= 4; i++)
				for (int j = 1; j <= 21; j++)
					if (this -> used[i][j])
						player_score[i] += block[j].num;
			if (player_score[1] + player_score[3] <= player_score[2] + player_score[4])
				this -> victor = 2;
			else this -> victor = 1;
		}
	}
	void print() {
		FILE* replay = fopen("replay.txt","a");
		fprintf(replay,"player%d`s move: \n",this -> last_move);
		for (int i = 1; i <= 20; i++) {
			for (int j = 1; j <= 20; j++)
				fprintf(replay,"%d",this -> color[i][j]);
			fprintf(replay,"\n");
		}
		fclose(replay);
	}
};
struct board*  Game = new board();

Judge * attacker = NULL, * defender = NULL;
Judge::Judge(string name)
{
	this->player_name = name;
	this->won = 0;
}

void Judge::started()
{
	if (this->player_name == "attacker")
		attacker = this;
	else
		defender = this;
	fprintf(stderr , "%s started.\n" , this->player_name.c_str());
	if (attacker && defender)
	{
		Game -> start();
		fprintf(stderr , "%s\n" , "Game successfully initialized.");
	}
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
	fprintf(stderr , "writing to %s: %s\n" , this->player_name.c_str() , Game -> last_command.c_str());
	l.push_back(Game -> last_command);
	list ret;
	for (vector<string>::iterator it = l.begin() ; it != l.end() ; ++it)
		ret.append(*it);
	return ret;
}

int Judge::after_read(string line)
{
	if (Game -> over) return 1;
	fprintf(stderr , "read from %s: %s\n" , this->player_name.c_str() , line.c_str());
	if (!board::normalize_input(line))
	{
		Game -> victor = (this -> player_name == "attacker") ? 2 :1;
		Game -> over = true;
		fprintf(stderr , "game over due to %s.\n" , "illegal output");
		return 3;
	}
	replay.push_back(line);
	if (!Game -> judge_pass(line))
	{
		Game -> victor = (this -> player_name == "attacker") ? 2 :1;
		Game -> over = true;
		fprintf(stderr , "game over due to %s.\n" , "illegal movement");
		return 2;
	}
	else
	{
		Game -> addGamebox(line);
		Game -> last_command = line;
		Game -> print(); // use to debug
	}

	if (Game -> over)
	{
		fprintf(stderr , "game over due to %s.\n" , "4 empty movements");
		this-> won = ((this -> player_name == "attacker")&&(Game -> victor == 1)) || ((this -> player_name == "defender")&&(Game -> victor == 2) );
		return 1;
	}
	return 0;
}

int Judge::victorious()
{
	return this->won;
}

StrVector pull_replay()
{
	list ret;
	for (vector<string>::iterator it = replay.begin() ; it != replay.end() ; ++it)
		ret.append(*it);
	return ret;
}

BOOST_PYTHON_MODULE(judge_ext)
{
	using namespace boost::python;
	class_<Judge>("Judge" , init<string>())
	.def("started", &Judge::started)
	.def("dead", &Judge::dead)
	.def("before_write", &Judge::before_write)
	.def("on_write", &Judge::on_write)
	.def("after_read", &Judge::after_read)
	.def("victorious", &Judge::victorious)
	.def_readwrite("player_name", &Judge::player_name);
	def("pull_replay", pull_replay);
}

