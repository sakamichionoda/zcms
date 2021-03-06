{% extends "../../../templates/frontend/default/index.volt" %}
{% block content %}
    <div class="container user-control">
        <div class="col-md-4 col-sm-6 col-md-offset-4 col-sm-offset-2">
            {% include _flashSession %}
            <div class="row">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title">{{ 'Forgot Your Password'|t }}</h3>
                    </div>
                    <div class="panel-body">
                        <form action="{{ _baseUri }}/user/forgot-password/" method="post">
                            <div class="form-group">
                                <label for="email">{{ 'Email'|t }}</label>
                                <input type="text" class="form-control" id="email" name="email" placeholder="{{ 'Enter your email address'|t }}" value="">
                            </div>
                            <div class="form-group">
                                <input type="submit" class="btn btn-default" value="{{ 'Submit'|t }}">
                            </div>
                            <div class="form-group">
                                <a href="{{ _baseUri }}/user/login/">{{ 'Log in'|t }}</a> | or <a href="{{ _baseUri }}/user/register/">{{ 'Register an account' }}</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
{% endblock %}